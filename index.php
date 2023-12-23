<?php

include 'components/connect.php';

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
        ?>

        <div class="featured-products-container">
            <?php foreach ($featured_products as $product) : ?>
                <div class="product-card">
                    <a class="image-container" href="product_view.php">
                        <img src="uploaded_img/<?php echo $product['image_01']; ?>" alt="product">
                    </a>
                    <div class="details">
                        <span class="category-name"><?php echo $product['category']; ?></span>
                        <a href="product_view.php">
                            <span class="product-name"><?php echo $product['name']; ?></span>
                        </a>
                        <hr>
                        <div class="loop-btn">
                            <span class="price">Rs. <?php echo number_format($product['price']); ?></span>
                            <a href="" class="option-btn">add to cart</a>
                        </div>
                    </div>
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

                <div class="grid-container">

                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/iphone5s-1-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">cellphones</span>
                            <span class="product-name">Apple iPhone 5s</span>
                            <hr>
                            <span class="price">Rs. 124,000.00</span>
                        </div>
                    </div>
                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/ipad-mini-01-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">computers & tablets</span>
                            <span class="product-name">Apple iPad Mini</span>
                            <span class="price">Rs. 235,000.00</span>
                        </div>
                    </div>
                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/iphone6-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">cellphones</span>
                            <span class="product-name">Apple iPhone 6</span>
                            <span class="price">Rs. 140,000.00</span>
                        </div>
                    </div>

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

                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/beats-narkitecture-01-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">Accessories</span>
                            <span class="product-name">Beats Headphone 2</span>
                            <hr>
                            <span class="price">Rs. 34,000.00</span>
                        </div>
                    </div>
                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/denon-head-01-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">Accessories</span>
                            <span class="product-name">Denon Headphones</span>
                            <hr>
                            <span class="price">Rs. 24,000.00</span>
                        </div>
                    </div>
                    <div class="product-card">
                        <a class="image-container" href="#">
                            <img src="img/imac_n-300x300.jpg" alt="product">
                        </a>
                        <div class="details">
                            <span class="category-name">computers & tablets</span>
                            <span class="product-name">Apple iMac 27-inch</span>
                            <hr>
                            <span class="price">Rs. 124,000.00</span>
                        </div>
                    </div>

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