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
    <link rel="stylesheet" href="fonts/remixicon.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.3.0/simplebar.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>

    <?php include 'components/sidebar.php' ?>

    <div class="page-content">

        <?php include 'components/header.php'; ?>

        <?php
        $selectedCategory = isset($_GET['category']) ? $_GET['category'] : "";

        // Fetch the category information based on the name
        $select_category_id = $conn->prepare("SELECT id FROM categories WHERE name = :category_name");
        $select_category_id->bindParam(':category_name', $selectedCategory);
        $select_category_id->execute();
        $category_info = $select_category_id->fetch(PDO::FETCH_ASSOC);

        // Check if the category ID is found
        if (!$category_info) {
            echo "Category not found!";
            exit();
        }

        $selectedCategoryID = $category_info['id'];
        function getCategoryHierarchy($conn, $categoryID)
        {
            $hierarchy = [];

            $select_category_hierarchy = $conn->prepare("SELECT id, name, parent_id FROM categories WHERE id = :category_id");
            $select_category_hierarchy->bindParam(':category_id', $categoryID);
            $select_category_hierarchy->execute();
            $category = $select_category_hierarchy->fetch(PDO::FETCH_ASSOC);

            while ($category) {
                array_unshift($hierarchy, $category);
                $category = getParentCategory($conn, $category['parent_id']);
            }

            return $hierarchy;
        }

        function getParentCategory($conn, $parentID)
        {
            $select_parent_category = $conn->prepare("SELECT id, name, parent_id FROM categories WHERE id = :parent_id");
            $select_parent_category->bindParam(':parent_id', $parentID);
            $select_parent_category->execute();
            return $select_parent_category->fetch(PDO::FETCH_ASSOC);
        }

        ?>

        <div class="site-header-overlay">
            <img class="bg-image" src="img/home-appliances.jpg" alt="bg-img" id="parallax-image">
            <div class="overlay"></div>
            <h1 class="page-title"><?= $selectedCategory ?></h1>
            <div class="links">
                <a href="index.php"><span class="home">Discount Center</span></a>
                <span class="page-name"> > </span>
                <a href="shop.php"><span class="home">Shop</span></a>
                <?php
                // Display the category hierarchy dynamically
                $hierarchy = getCategoryHierarchy($conn, $selectedCategoryID);
                foreach ($hierarchy as $index => $category) {
                    echo '<span>';
                    echo '<span class="page-name"> > </span>';
                    if ($index < count($hierarchy) - 1) {
                        // Not the last element, so add a link
                        echo '<a class="home" href="category.php?category=' . $category['name'] . '">' . $category['name'] . '</a>';
                    } else {
                        // Last element, add the class page-name
                        echo '<span class="page-name">' . $category['name'] . '</span>';
                    }
                    echo '</span>';
                }
                ?>
            </div>
        </div>

        <section class="shop">

            <?php
            // Function to fetch subcategories and calculate item count
            function fetchSubcategory($conn, $categoryID)
            {
                $subcategories = [];

                $select_subcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = :category_id");
                $select_subcategories->bindParam(':category_id', $categoryID);
                $select_subcategories->execute();
                $subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);

                foreach ($subcategories as &$subcategory) {
                    $countQuery = $conn->prepare("SELECT COUNT(*) as item_count FROM products WHERE category = ?");
                    $countQuery->execute([$subcategory['id']]);
                    $itemCount = $countQuery->fetch(PDO::FETCH_ASSOC)['item_count'];

                    // Recursive call to get subcategories
                    $subcategory['subcategories'] = fetchSubcategory($conn, $subcategory['id']);

                    foreach ($subcategory['subcategories'] as $subsubcategory) {
                        $itemCount += $subsubcategory['item_count'];
                    }

                    $subcategory['item_count'] = $itemCount;
                }

                return $subcategories;
            }

            // Fetch the category information based on the name
            $select_category_id = $conn->prepare("SELECT id FROM categories WHERE name = :category_name");
            $select_category_id->bindParam(':category_name', $selectedCategory);
            $select_category_id->execute();
            $category_info = $select_category_id->fetch(PDO::FETCH_ASSOC);

            $selectedCategoryID = $category_info['id'];

            // Fetch subcategories and calculate item count recursively
            $subcategories = fetchSubcategory($conn, $selectedCategoryID);
            ?>

            <?php $totalSubcategories = count($subcategories); ?>
            <?php if ($index < $totalSubcategories - 1) : ?>
                <div class="category-separator">
                    <?php foreach ($subcategories as $index => $subcategory) : ?>
                        <a href="category.php?category=<?= urlencode($subcategory['name']) ?>" class="category-card">
                            <div class="category-desc">
                                <span class="category-name"><?= $subcategory['name'] ?></span>
                                <span class="item-qty"><?= $subcategory['item_count'] ?> items</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>





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

            // Fetch all subcategories and sub-subcategories under the selected category
            $category_query = "
SELECT id
FROM categories
WHERE id = :category_id OR parent_id = :category_id OR parent_id IN (
    SELECT id FROM categories WHERE parent_id = :category_id
)
";

            try {
                // Fetch the minimum and maximum prices from the products table for the selected categories
                $price_query = "
    SELECT MIN(price) as min_price, MAX(price) as max_price
    FROM products
    WHERE category IN ({$category_query})
";
                $price_result = $conn->prepare($price_query);
                $price_result->bindValue(':category_id', $selectedCategoryID, PDO::PARAM_INT);
                $price_result->execute();
                $price_range = $price_result->fetch(PDO::FETCH_ASSOC);

                // Set default values if no products are available
                $minPrice = isset($price_range['min_price']) ? $price_range['min_price'] : 0;
                $maxPrice = isset($price_range['max_price']) ? $price_range['max_price'] : 500000;
            } catch (PDOException $e) {
                // Handle any database errors
                die("Error: " . $e->getMessage());
            }


            ?>

            <?php

            include 'components/fetch_products_by_category.php';

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
                                <a href="?category=<?= $selectedCategory ?>&page=1&orderby=<?= $orderby ?>&products_per_page=<?= $products_per_page ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>" class="filter" id="filterButton">filter</a>
                            </div>
                        </div>
                    </div>


                    <div class="column">
                        <h2 class="column-title">latest products</h2>


                        <?php
                        // Assuming you have a function to fetch the latest products from the database
                        function getLatProducts($conn, $limit = 5)
                        {
                            $select_latest_products = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit");
                            $select_latest_products->bindParam(':limit', $limit, PDO::PARAM_INT);
                            $select_latest_products->execute();

                            return $select_latest_products->fetchAll(PDO::FETCH_ASSOC);
                        }

                        // Fetch the latest 5 products
                        $latest_products = getLatProducts($conn, 5);

                        // Generate HTML for each product
                        foreach ($latest_products as $product) :
                        ?>
                            <div class="product-widget">
                                <a class="img-container" href="product_view.php?pid=<?php echo $product['id']; ?>">
                                    <img src="uploaded_img/<?php echo $product['image_01']; ?>" alt="<?php echo $product['name']; ?>">
                                </a>
                                <div class="details">
                                    <a class="name" href="product_view.php?pid=<?php echo $product['id']; ?>">
                                        <span title="<?php echo $product['name']; ?>" class="product-name"><?php echo $product['name']; ?></span>
                                    </a>
                                    <div class="prices">
                                        <span class="newprice">Rs.<?php echo number_format($product['price']); ?></span>
                                        <span class="oldprice">Rs.<?php echo number_format($product['old_price']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="shop-products-container">

                    <div class="shop-top-bar">
                        <div class="showing-list">showing <?= $start_range ?>-<?= $end_range ?> of <?= $total_products ?> results</div>

                        <div class="dropdowns">
                            <div class="custom-dropdown">
                                <div class="dropdown-toggle" id="productsPerPage"><?= $display_perpage ?></div>
                                <div class="dropdown-list">
                                    <a href="?category=<?= $selectedCategory ?>&page=1&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>&orderby=<?= $orderby ?>&products_per_page=16" class="dropdown-item 1" data-value="16">16</a>
                                    <a href="?category=<?= $selectedCategory ?>&page=1&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>&orderby=<?= $orderby ?>&products_per_page=28" class="dropdown-item 1" data-value="28">28</a>
                                    <a href="?category=<?= $selectedCategory ?>&page=1&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>&orderby=<?= $orderby ?>&products_per_page=40" class="dropdown-item 1" data-value="40">40</a>
                                    <a href="?category=<?= $selectedCategory ?>&page=1&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>&orderby=<?= $orderby ?>&products_per_page=All" class="dropdown-item 1" data-value="All">All</a>
                                </div>
                            </div>

                            <div class="custom-dropdown">
                                <div class="dropdown-toggle" id="sort-dropdown" onclick="toggleDropdown('orderby-dropdown')"><?= $display_orderby ?></div>
                                <div class="dropdown-list">
                                    <a href="#" onclick="changeOrder('latest')" class="dropdown-item <?= ($orderby === 'latest') ? 'active' : '' ?>" id="latest" data-value="latest" data-sort="latest">latest</a>
                                    <a href="#" onclick="changeOrder('priceLowToHigh')" class="dropdown-item <?= ($orderby === 'priceLowToHigh') ? 'active' : '' ?>" id="priceLowToHigh" data-value="price (low to high)" data-sort="priceLowToHigh">price (low to high)</a>
                                    <a href="#" onclick="changeOrder('priceHighToLow')" class="dropdown-item <?= ($orderby === 'priceHighToLow') ? 'active' : '' ?>" id="priceHighToLow" data-value="price (high to low)" data-sort="priceHighToLow">price (high to low)</a>
                                    <a href="#" onclick="changeOrder('a-z')" class="dropdown-item <?= ($orderby === 'a-z') ? 'active' : '' ?>" id="a-z" data-value="a-z" data-sort="a-z">a-z</a>
                                    <a href="#" onclick="changeOrder('z-a')" class="dropdown-item <?= ($orderby === 'z-a') ? 'active' : '' ?>" id="z-a" data-value="z-a" data-sort="z-a">z-a</a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php if (empty($products)) {
                        echo '<p class="empty">no products found!</p>';
                    } ?>

                    <div id="filteredProductsContainer" class="grid-container">

                        <?php foreach ($products as $product) : ?>
                            <form action="" method="post">
                                <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                <input type="hidden" name="image" value="<?php echo $product['image_01']; ?>">
                                <input type="hidden" name="qty" value="1">
                                <div class="product-card <?php echo ($product['availability'] === 'out of stock') ? 'out-of-stock' : ''; ?>" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>">
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

                    <?php
                    $total_pages = ceil($total_products / $products_per_page);

                    echo '<div class="page-numbers">';
                    if ($current_page > 1) {
                        echo '<a href="?category=' . $selectedCategory . '&page=' . ($current_page - 1) . '&products_per_page=' . $products_per_page . '&orderby=' . $orderby . '" class="arrow-btn"><i class="ri-arrow-left-line"></i></a>';
                    }
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($current_page == $i) {
                            echo '<span class="btn active">' . $i . '</span>';
                        } else {
                            echo '<a href="?category=' . $selectedCategory . '&page=' . $i . '&products_per_page=' . $products_per_page . '&orderby=' . $orderby . '" class="btn">' . $i . '</a>';
                        }
                    }
                    if ($current_page < $total_pages) {
                        echo '<a href="?category=' . $selectedCategory . '&page=' . ($current_page + 1) . '&products_per_page=' . $products_per_page . '&orderby=' . $orderby . '" class="arrow-btn"><i class="ri-arrow-right-line"></i></a>';
                    }
                    echo '</div>';
                    ?>


                </div>
            </div>

        </section>

        <?php include 'components/footer.php'; ?>

    </div>

    <script src="js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        function toggleDropdown(dropdownId) {
            var dropdown = document.getElementById(dropdownId);
            dropdown.classList.toggle("active");
        }

        function changeOrder(order) {
            // Modify the URL or perform any other action based on the selected order
            window.location.href = `?category=<?= $selectedCategory ?>&page=1&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>&products_per_page=<?= $products_per_page ?>&orderby=${order}`;
        }

        document.getElementById('sort-dropdown').addEventListener('click', function() {
            // Get the selected sorting criteria
            var selectedSort = event.target.dataset.sort;

            // Get the container element that holds the products
            var productsContainer = document.getElementById('filteredProductsContainer');

            // Get all product items within the container
            var products = Array.from(productsContainer.getElementsByClassName('product-card'));

            // Function to compare products based on the selected sorting criteria
            function compareProducts(a, b) {
                // Logic to compare products based on your selected criteria
                // Modify this according to your requirements
                var aValue = a.dataset.productName; // Update with the actual data attribute
                var bValue = b.dataset.productName; // Update with the actual data attribute

                if (selectedSort === 'priceLowToHigh') {
                    return aValue - bValue;
                } else if (selectedSort === 'priceHighToLow') {
                    return bValue - aValue;
                } else if (selectedSort === 'a-z') {
                    return aValue.localeCompare(bValue);
                } else if (selectedSort === 'z-a') {
                    return bValue.localeCompare(aValue);
                } else {
                    // Default to sorting by latest if no criteria matched
                    return 0;
                }
            }

            // Sort the array based on the compare function
            products.sort(compareProducts);

            // Remove existing products from the container
            while (productsContainer.firstChild) {
                productsContainer.removeChild(productsContainer.firstChild);
            }

            // Append sorted products back to the container
            products.forEach(function(product) {
                productsContainer.appendChild(product);
            });
        });



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

            // Check if there are URL parameters for min_price and max_price
            var urlParams = new URLSearchParams(window.location.search);
            var minPriceParam = urlParams.get('min_price');
            var maxPriceParam = urlParams.get('max_price');

            if (minPriceParam !== null && maxPriceParam !== null) {
                // Update only the handles without changing the entire range
                $("#price-slider").slider("values", [minPriceParam, maxPriceParam]);
                // Update the price-range span
                $("#price-range").text("Rs. " + minPriceParam.toLocaleString() + " - Rs. " + maxPriceParam.toLocaleString());
            }

            $("#price-slider").on("slidestop", function(event, ui) {
                var minPrice = ui.values[0];
                var maxPrice = ui.values[1];
                var currentURL = new URL(window.location.href);

                // Update the URL parameters with the new min_price and max_price
                currentURL.searchParams.set('min_price', minPrice);
                currentURL.searchParams.set('max_price', maxPrice);

                // Set the filterButton href attribute to the updated URL
                $("#filterButton").attr("href", currentURL.href);
            });


        });


        $(document).ready(function() {

            // Toggle dropdown list visibility on toggle click
            $('.custom-dropdown .dropdown-toggle').on('click', function() {
                var dropdown = $(this).closest('.custom-dropdown');
                dropdown.find('.dropdown-list').toggleClass('active');
            });

            // Handle item click in the dropdown list
            $('.custom-dropdown .dropdown-item.1').on('click', function() {
                var selectedValue = $(this).data('value');
                var dropdown = $(this).closest('.custom-dropdown');

                // Update the toggle button text with the selected value
                dropdown.find('.dropdown-toggle').text(selectedValue);

                // Do something with the selected value (e.g., update UI, trigger an event, etc.)
                console.log('Selected value:', selectedValue);

            });

            // Handle item click in the dropdown list
            $('.custom-dropdown .dropdown-item.2').on('click', function() {
                var selectedValue = $(this).data('value');
                var dropdown = $(this).closest('.custom-dropdown');

                // Update the toggle button text with the selected value
                dropdown.find('.dropdown-toggle').text(selectedValue);

                // Do something with the selected value (e.g., update UI, trigger an event, etc.)
                console.log('Selected value:', selectedValue);

                // dropdown.find('.dropdown-list').removeClass('active');
            });

            // Hide dropdown list on document click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.custom-dropdown').length) {
                    $('.custom-dropdown .dropdown-list').removeClass('active');
                }
            });

        });
    </script>

</body>

</html>