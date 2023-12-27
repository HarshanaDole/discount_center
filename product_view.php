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
</head>

<body>

    <?php include 'components/sidebar.php' ?>

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
                                <div class="button-container">
                                    <input type="number" name="qty" class="quantity" id="quantity" value="1" min="1" max="99" onclick="event.preventDefault()" onkeypress="if(this.value.length == 2) return false;">
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

                </section>


                <?php include 'components/footer.php'; ?>

    </div>


    <script src="js/script.js"></script>


    <script>
        // Function to update the quantity
        function updateQuantity(change) {
            var quantityElement = document.getElementById("quantity");
            var currentQuantity = parseInt(quantityElement.value);

            // Update the quantity based on the change parameter
            var newQuantity = currentQuantity + change;

            // Ensure the quantity doesn't go below 1
            if (newQuantity >= 1) {
                quantityElement.value = newQuantity;
            }
        }
    </script>



</body>