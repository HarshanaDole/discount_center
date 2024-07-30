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
    header('Location: product_view.php?pid=' . $_POST['pid']);
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</head>
<body>
    <?php include 'components/sidebar.php'; ?>
    <div class="page-content">
        <?php include 'components/header.php'; ?>
        <?php
        $pid = $_GET['pid'];
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $select_products->execute([$pid]);
        if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <section class="product-view">
            <form action="" method="post">
                <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
                <div class="row">
                    <div class="image-container">
                        <img src="uploaded_img/<?php echo $fetch_product['image_01']; ?>" alt="<?php echo $fetch_product['name']; ?>">
                        <div class="other-img">
                            <img src="uploaded_img/<?php echo $fetch_product['image_01']; ?>" alt="<?php echo $fetch_product['name']; ?>">
                            <img src="uploaded_img/<?php echo $fetch_product['image_02']; ?>" alt="<?php echo $fetch_product['name']; ?>">
                            <img src="uploaded_img/<?php echo $fetch_product['image_03']; ?>" alt="<?php echo $fetch_product['name']; ?>">
                        </div>
                    </div>
                    <div class="product-info">
                        <h1 class="product-name"><?php echo $fetch_product['name']; ?></h1>
                        <span class="price">Rs. <?php echo number_format($fetch_product['price']); ?></span>
                        <div class="description"><?php echo $fetch_product['description']; ?></div>

                        <!-- Product Preferences Section -->
                        <div class="product-preferences">
                            <div class="preference-item">
                                <label for="capacity">Capacity:</label>
                                <div class="options">
                                    <span class="option" data-value="small">100L</span>
                                    <span class="option" data-value="medium">200L</span>
                                    <span class="option" data-value="large">300L</span>
                                    <span class="option" data-value="xl">400L</span>
                                </div>
                                <input type="hidden" name="capacity" id="capacity" value="small">
                            </div>
                            <div class="preference-item">
                                <label for="color">Color:</label>
                                <div class="options">
                                    <span class="option color-option" style="background-color: red;" data-value="red"></span>
                                    <span class="option color-option" style="background-color: blue;" data-value="blue"></span>
                                    <span class="option color-option" style="background-color: green;" data-value="green"></span>
                                    <span class="option color-option" style="background-color: black;" data-value="black"></span>
                                </div>
                                <input type="hidden" name="color" id="color" value="red">
                            </div>
                            <div class="preference-item">
                                <label for="inverter">Inverter:</label>
                                <div class="options">
                                    <span class="option" data-value="inverter">Inverter</span>
                                    <span class="option" data-value="non-inverter">Non-Inverter</span>
                                </div>
                                <input type="hidden" name="inverter" id="inverter" value="inverter">
                            </div>
                        </div>

                        <div class="button-container">
                            <div class="adjust" id="adjust">
                                <button class="up" onclick="updateQuantity(1); event.preventDefault()">+</button>
                                <button class="down" onclick="updateQuantity(-1); event.preventDefault()">-</button>
                            </div>
                            <?php
                            if ($fetch_product['availability'] === 'out of stock') {
                                echo '<input type="submit" value="out of stock" class="btn out-of-stock" name="out_of_stock" disabled>';
                            } else {
                                echo '<input type="submit" value="add to cart" class="btn" name="add_to_cart">';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="additional-info">
                        <div class="info-item">
                            <img src="img/free-shipping.png" alt="Free Shipping">
                            <div>
                                <h4>FREE SHIPPING</h4>
                                <p>Free shipping on all orders over $99.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <img src="img/money-back.png" alt="Money Back Guarantee">
                            <div>
                                <h4>MONEY BACK GUARANTEE</h4>
                                <p>100% money back guarantee.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <img src="img/24-hours-support.png" alt="Online Support 24/7">
                            <div>
                                <h4>ONLINE SUPPORT 24/7</h4>
                                <p>Lorem ipsum dolor sit amet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
            function getCategoryHierarchy($conn, $category_id)
            {
                try {
                    // Fetch category information
                    $select_category = $conn->prepare("SELECT id, name, parent_id FROM categories WHERE id = :id");
                    $select_category->bindParam(':id', $category_id);
                    $select_category->execute();
                    $category_info = $select_category->fetch(PDO::FETCH_ASSOC);

                    if ($category_info) {
                        // Check if the category is already a main category
                        if ($category_info['parent_id'] == 0) {
                            return array($category_info);
                        } else {
                            // Recursively get the hierarchy for the parent category
                            $parent_hierarchy = getCategoryHierarchy($conn, $category_info['parent_id']);

                            // Add the current category to the hierarchy
                            $parent_hierarchy[] = $category_info;

                            return $parent_hierarchy;
                        }
                    }

                    return array(); // Return an empty array if category information is not found
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                    return array(); // Return an empty array in case of an error
                }
            }
            ?>
            <div class="row">
                <div class="product-meta">
                    <span class="model-wrapper">model: <span class="model"><?php echo $fetch_product['model']; ?></span></span>
                    <span class="categories-wrapper">categories:
                        <?php
                        $categoryHierarchy = getCategoryHierarchy($conn, $fetch_product['category']);
                        $totalCategories = count($categoryHierarchy);

                        foreach ($categoryHierarchy as $index => $category) {
                            echo '<span class="categories">' . $category['name'] . '</span>';

                            // Add separator if not the last category
                            if ($index < $totalCategories - 1) {
                                echo ' > ';
                            }
                        }
                        ?>
                    </span>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<p class="empty">no products added yet!</p>';
        }
        ?>
        <!-- Tabbed Section Start -->
        <div class="product-tabs">
            <ul class="tab-list">
                <li class="tab-item active" data-tab="description">Description</li>
                <li class="tab-item" data-tab="additional-info">Additional information</li>
                <li class="tab-item" data-tab="reviews">Reviews (0)</li>
            </ul>
            <div class="tab-content">
                <div id="description" class="tab-pane active">
                    <p>The new range of LG Frost Free Refrigerators with cutting edge Smart 
                    Inverter Compressor technology that takes energy efficiency to another level and helps you save more. LG Door Cooling+™ 
                    makes inside temperature more even and cools the refrigerator 35% faster than the conventional cooling system. This 
                    reduces the temperature gap between the inner part and the door side of the compartment; thus letting the food remain 
                    fresh for long. MOIST ‘N' FRESH is an innovative lattice-patterned box cover which maintains the moisture at the optimal level.</p>
                </div>
                <div id="additional-info" class="tab-pane">
                    <div class="additional-information">
                        <table class="additional-details">
                            <tr>
                                <th>Brand</th>
                                <td>LG</td>
                            </tr>
                            <tr>
                                <th>Cooling</th>
                                <td>Ice Beam Door Cooling</td>
                            </tr>
                            <tr>
                                <th>Compressor</th>
                                <td>Smart Inverter</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>52.5 Kg</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="reviews" class="tab-pane">
                    <p>There are no reviews yet.</p>
                </div>
            </div>
        </div>
        </section>
        <!-- Tabbed Section End -->

        <?php include 'components/footer.php'; ?>
    </div>
    <script src="js/script.js"></script>
    <script>
        // Function to update the quantity
        function updateQuantity(amount) {
            var quantityInput = document.querySelector('input[name="quantity"]');
            var currentQuantity = parseInt(quantityInput.value);
            var newQuantity = currentQuantity + amount;
            if (newQuantity < 1) {
                newQuantity = 1;
            }
            quantityInput.value = newQuantity;
        }

        // Tab functionality
        document.addEventListener('DOMContentLoaded', () => {
            const tabItems = document.querySelectorAll('.tab-item');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Remove active class from all tab items and panes
                    tabItems.forEach(tab => tab.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));

                    // Add active class to clicked tab item and corresponding pane
                    item.classList.add('active');
                    document.getElementById(item.getAttribute('data-tab')).classList.add('active');
                });
            });
        });

        // Preference selection functionality
        const preferenceItems = document.querySelectorAll('.preference-item .option');
        preferenceItems.forEach(option => {
            option.addEventListener('click', () => {
                const parent = option.closest('.preference-item');
                const input = parent.querySelector('input[type="hidden"]');
                const value = option.getAttribute('data-value');

                // Deselect all options in the same group
                parent.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));

                // Select the clicked option
                option.classList.add('selected');
                input.value = value;
            });
        });
    </script>
</body>
</html>
