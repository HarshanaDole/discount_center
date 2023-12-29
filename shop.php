<?php

include 'components/connect.php';

session_start();

if (isset($_COOKIE['session_id'])) {
    $session_id = $_COOKIE['session_id'];
} else {
    $session_id = uniqid();
    setcookie('session_id', $session_id, time() + (86400 * 30), "/"); // set cookie to expire in 30 days
}

include 'components/wishlist_cart.php';

if (isset($_POST['add_to_cart'])) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Center</title>
    <link rel="icon" href="img/tablogo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.3.0/simplebar.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>

    <?php include 'components/sidebar.php' ?>

    <div class="page-content">

        <?php include 'components/header.php'; ?>

        <div class="site-header-overlay">
            <img class="bg-image" src="img/home-appliances.jpg" alt="bg-img" id="parallax-image">
            <div class="overlay"></div>
            <h1 class="page-title">shop</h1>
            <div class="links">
                <a href="index.php"><span class="home">Discount Center</span></a>
                <span class="page-name"> > Shop</span>
            </div>
        </div>

        <section class="shop">

            <div class="separator">
                <h4>Featured Products</h4>
                <span class="line"></span>
            </div>

            <?php
            // Fetch featured products
            $select_featured_products = $conn->prepare("SELECT * FROM products WHERE featured = 1");
            $select_featured_products->execute();
            $featured_products = $select_featured_products->fetchAll(PDO::FETCH_ASSOC);

            function getMainCategoryName($conn, $category_id)
            {
                try {
                    // Fetch category information
                    $select_category = $conn->prepare("SELECT name, parent_id FROM categories WHERE id = :id");
                    $select_category->bindParam(':id', $category_id);
                    $select_category->execute();
                    $category_info = $select_category->fetch(PDO::FETCH_ASSOC);

                    if ($category_info) {
                        // Check if the category is already a main category
                        if ($category_info['parent_id'] == 0) {
                            return $category_info['name'];
                        } else {
                            // Recursively get the main category name for the parent category
                            $main_category_name = getMainCategoryName($conn, $category_info['parent_id']);

                            return $main_category_name;
                        }
                    }

                    return ''; // Return an empty string if category information is not found
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                    return ''; // Return an empty string in case of an error
                }
            }

            // Fetch the minimum and maximum prices from the products table
            $select_price_range = $conn->prepare("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products");
            $select_price_range->execute();
            $price_range = $select_price_range->fetch(PDO::FETCH_ASSOC);

            // Set default values if no products are available
            $minPrice = isset($price_range['min_price']) ? $price_range['min_price'] : 0;
            $maxPrice = isset($price_range['max_price']) ? $price_range['max_price'] : 500000;
            ?>


            <div class="shop-choices">

                <div class="filter-column">
                    <div class="filter-container">
                        <div class="price-filter-widget">
                            <h3 class="widget-title">filter by price</h3>
                            <div class="price-slider" id="price-slider"></div>
                            <div class="price-slider-amount">
                                <div class="price-label">
                                    <span class="price-range" id="price-range">Rs. <?php echo number_format($minPrice); ?> - Rs. <?php echo number_format($maxPrice); ?></span>
                                </div>
                                <button class="filter" id="filterButton">filter</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shop-products-container">

                    <div class="text-field">
                        <div class="heading">top choices</div>
                        <div class="subheading">discount center</div>
                    </div>

                    <?php
                    $select_products = $conn->prepare("SELECT * FROM `products`");
                    $select_products->execute();
                    $products = []; // Initialize an empty array to store the products

                    if ($select_products->rowCount() > 0) {
                        while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                            $products[] = $fetch_product; // Store each product in the array
                        }

                        // Function to sort products by price in ascending order (low to high)
                        function sortByPriceAndAvailability($a, $b)
                        {
                            // First, compare availability
                            $availabilityComparison = strcmp($a['availability'], $b['availability']);

                            // If availability is the same, compare prices
                            if ($availabilityComparison === 0) {
                                $priceA = (float) $a['price'];
                                $priceB = (float) $b['price'];

                                // If prices are the same, compare names alphabetically
                                if ($priceA === $priceB) {
                                    $nameComparison = strcmp($a['name'], $b['name']);
                                    return $nameComparison;
                                }

                                return $priceA - $priceB;
                            }

                            return $availabilityComparison;
                        }

                        // Sort the products array using the custom sort function
                        usort($products, 'sortByPriceAndAvailability');
                    } else {
                        echo '<p class="empty">no products found!</p>';
                    }
                    ?>

                    <div id="filteredProductsContainer" class="grid-container">

                        <?php foreach ($products as $product) : ?>
                            <form action="" method="post">
                                <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                <input type="hidden" name="image" value="<?php echo $product['image_01']; ?>">
                                <input type="hidden" name="qty" value="1">
                                <div class="product-card <?php echo ($product['availability'] === 'out of stock') ? 'out-of-stock' : ''; ?>">
                                    <a class="image-container" href="product_view.php?pid=<?php echo $product['id']; ?>">
                                        <img src="uploaded_img/<?php echo $product['image_01']; ?>" alt="product">
                                    </a>
                                    <div class="details">
                                        <span class="category-name">
                                            <?php
                                            $main_category_name = getMainCategoryName($conn, $product['category']);
                                            echo $main_category_name;
                                            ?>
                                        </span>
                                        <a href="product_view.php?pid=<?php echo $product['id']; ?>">
                                            <span class="product-name" title="<?php echo $product['name']; ?>"><?php echo $product['name']; ?></span>
                                        </a>
                                        <hr>
                                        <div class="loop-btn">
                                            <span class="price">Rs. <?php echo number_format($product['price']); ?></span>
                                            <?php
                                            if ($product['availability'] === 'out of stock') {
                                                echo '<button class="btn" type="submit" name="out_of_stock" disabled>Out of Stock</button>';
                                            } else {
                                                echo '<button class="option-btn" type="submit" name="add_to_cart">Add to Cart</button>';
                                            }
                                            ?>
                                            <button class="heart-icon fa-regular fa-heart" type="submit" name="add_to_wishlist"></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php endforeach; ?>

                    </div>

                    <div class="button">
                        <a href="" class="btn">Shop All Appliances</a>
                    </div>

                </div>
            </div>

        </section>

        <?php include 'components/footer.php'; ?>

    </div>

    <script src="js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(function() {
            // Initialize the price slider
            $("#price-slider").slider({
                range: true,
                min: <?php echo $minPrice; ?>,
                max: <?php echo $maxPrice; ?>,
                values: [<?php echo $minPrice; ?>, <?php echo $maxPrice; ?>],
                slide: function(event, ui) {
                    $("#price-range").text("Rs. " + ui.values[0] + " - Rs. " + ui.values[1]);
                }
            });
        });

        $(document).ready(function() {
            // Add an event listener for the filter button
            $('#filterButton').on('click', function() {
                // Get the selected price range values
                var minPrice = $("#price-slider").slider("values", 0);
                var maxPrice = $("#price-slider").slider("values", 1);

                // AJAX request to fetch filtered products
                $.ajax({
                    url: 'components/filter_products.php', // Create a new PHP file for handling the filtering logic
                    type: 'POST',
                    data: {
                        minPrice: minPrice,
                        maxPrice: maxPrice
                    },
                    success: function(response) {
                        // Update the container with the filtered products
                        $('#filteredProductsContainer').html(response);
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>

</body>

</html>