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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.3.0/simplebar.min.js"></script>
</head>

<body>

    <?php include 'components/sidebar.php' ?>

    <div class="page-content">

        <?php include 'components/header.php'; ?>

        <div class="featured">
            <div class="product-carousel">

                <div class="product-slide animate-slide">
                    <div class="big-bubble-bg"></div>
                    <div class="bubble-bg"></div>
                    <img src="img/jbl.webp" alt="Product 1" class="product-img">
                    <div class="product-logo">
                        <img src="img/jbl-logo.png" alt="product-logo">
                    </div>
                    <div class="product-text">
                        <p>Designed For Sound.</p>
                        <p>Tuned For Emotion.</p>
                        <p>Now Wireless.</p>
                        <p>JBL Charge 5</p>
                    </div>
                </div>

                <div class="product-slide animate-slide">
                    <div class="big-bubble-bg-2"></div>
                    <div class="bubble-bg-2"></div>
                    <img src="img/samsung.avif" alt="Product 2" class="product-img-2">
                    <div class="product-logo-2">
                        <img src="img/Samsung-Logo.png" alt="product-logo-2">
                    </div>
                    <div class="product-text-2">
                        <p>55 inch</p>
                        <p>Crystal UHD</p>
                        <p>Built-in IoT Hub</p>
                        <p>4K Smart TV</p>
                    </div>
                </div>

                <div class="product-slide animate-slide">
                    <div class="big-bubble-bg-3"></div>
                    <div class="bubble-bg-3"></div>
                    <img src="img/samsung-fridge.png" alt="Product 3" class="product-img-3">
                    <div class="product-logo-3">
                        <img src="img/jbl-logo.png" alt="product-logo-3">
                    </div>
                    <div class="product-text-3">
                        <p>Designed For Sound.</p>
                        <p>Tuned For Emotion.</p>
                        <p>Now Wireless.</p>
                        <p class="last-text-3" id="last-text-3">JBL Charge 5</p>
                    </div>
                </div>
            </div>
            <i id="prevButton" class="fa-solid fa-angle-left arrow-icon"></i>
            <i id="nextButton" class="fa-solid fa-angle-right arrow-icon"></i>
        </div>

        <?php
        // Fetch main categories (where parent_id is 0)
        $select_main_categories = $conn->prepare("SELECT * FROM categories WHERE parent_id = 0");
        $select_main_categories->execute();
        $main_categories = $select_main_categories->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="category-container">
            <div class="main-categories">
                <?php foreach ($main_categories as $category) : ?>
                    <?php
                    // Fetch the count of items for each category
                    $categoryId = $category['id'];
                    $countQuery = $conn->prepare("SELECT COUNT(*) as item_count FROM products WHERE category = ?");
                    $countQuery->execute([$categoryId]);
                    $itemCount = $countQuery->fetch(PDO::FETCH_ASSOC)['item_count'];
                    ?>
                    <a href="#" class="category">
                        <img src="uploaded_img/<?php echo $category['icon']; ?>" alt="<?php echo $category['name']; ?>">
                        <div class="text-container">
                            <span class="heading"><?php echo $category['name']; ?></span>
                            <!-- You can retrieve the number of items associated with each category here -->
                            <span class="subheading"><?php echo $itemCount; ?> items</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>


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
        ?>

        <div class="featured-products-container">
            <?php foreach ($featured_products as $product) : ?>
                <div class="product-card">
                    <form method="post">
                        <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                        <input type="hidden" name="image" value="<?php echo $product['image_01']; ?>">
                        <input type="hidden" name="qty" value="1">
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
                                <button class="option-btn" type="submit" name="add_to_cart">Add to Cart</button>
                                <button class="heart-icon fa-regular fa-heart" type="submit" name="add_to_wishlist"></button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>


        <div class="top-choices">
            <div class="bg-image">
                <img src="img/home-appliances.jpg" alt="left-img">
            </div>

            <div class="top-products-container">

                <div class="text-field">
                    <div class="heading">top choices</div>
                    <div class="subheading">discount center</div>
                </div>

                <?php
                function getAllCategoryIds($conn, $category_id, &$categoryIds)
                {
                    $categoryIds[] = $category_id;

                    $select_subcategories = $conn->prepare("SELECT id FROM categories WHERE parent_id = :category_id");
                    $select_subcategories->bindParam(':category_id', $category_id);
                    $select_subcategories->execute();
                    $subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($subcategories as $subcategory) {
                        getAllCategoryIds($conn, $subcategory['id'], $categoryIds);
                    }
                }

                // Function to fetch products for multiple categories
                function getProductsByCategories($conn, $categoryIds, $limit = 3)
                {
                    $inClause = implode(',', array_map(function ($index) {
                        return ":category_$index";
                    }, array_keys($categoryIds)));

                    $select_products = $conn->prepare("SELECT * FROM products WHERE category IN ($inClause) ORDER BY id DESC LIMIT :limit");

                    foreach ($categoryIds as $index => $categoryId) {
                        $paramName = ":category_$index";
                        $select_products->bindValue($paramName, $categoryId, PDO::PARAM_INT);
                    }

                    $select_products->bindParam(':limit', $limit, PDO::PARAM_INT);
                    $select_products->execute();

                    return $select_products->fetchAll(PDO::FETCH_ASSOC);
                }


                $main_category_id_1 = 3;
                $categoryIds_1 = [];
                getAllCategoryIds($conn, $main_category_id_1, $categoryIds_1);
                $products_1 = getProductsByCategories($conn, $categoryIds_1);

                $main_category_id_2 = 13;
                $categoryIds_2 = [];
                getAllCategoryIds($conn, $main_category_id_2, $categoryIds_2);
                $products_2 = getProductsByCategories($conn, $categoryIds_2);
                ?>

                <div class="grid-container">

                    <?php foreach ($products_1 as $product) : ?>
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="image" value="<?php echo $product['image_01']; ?>">
                            <input type="hidden" name="qty" value="1">
                            <div class="product-card">
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
                                        <button class="option-btn" type="submit" name="add_to_cart">Add to Cart</button>
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

        <div class="top-choices">

            <div class="top-products-container">

                <div class="text-field-right">
                    <div class="heading">top choices</div>
                    <div class="subheading">discount center</div>
                </div>

                <div class="grid-container">

                    <?php foreach ($products_2 as $product) : ?>
                        <form action="" method="post">
                            <input type="hidden" name="pid" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="image" value="<?php echo $product['image_01']; ?>">
                            <input type="hidden" name="qty" value="1">
                            <div class="product-card">
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
                                        <button class="option-btn" type="submit" name="add_to_cart">Add to Cart</button>
                                        <button class="heart-icon fa-regular fa-heart" type="submit" name="add_to_wishlist"></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>

                </div>

                <div class="button-right">
                    <a href="" class="btn">Shop All Appliances</a>
                </div>

            </div>


            <div class="bg-image">
                <img src="img/home-appliances.jpg" alt="right-img">
            </div>

        </div>

        <?php include 'components/footer.php'; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="js/script.js"></script>

</body>

</html>