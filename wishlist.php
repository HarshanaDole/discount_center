<?php

include 'components/connect.php';

session_start();

if (isset($_COOKIE['session_id'])) {
    $session_id = $_COOKIE['session_id'];
} else {
    $session_id = uniqid();
    setcookie('session_id', $session_id, time() + (86400 * 30), "/"); // set cookie to expire in 30 days
}

if (isset($_POST['delete'])) {
    $wishlist_id = $_POST['wishlist_id'];
    $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
    $delete_wishlist_item->execute([$wishlist_id]);
    header('Location: wishlist.php');
    exit();
}

include 'components/wishlist_cart.php';

if (isset($_POST['add_to_cart'])) {
    header('Location: wishlist.php');
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
    <script>
        function updateQuantity(pid, change) {
            var quantityElement = document.getElementById("quantity_" + pid);
            var currentQuantity = parseInt(quantityElement.value);

            // Update the quantity based on the change parameter
            var newQuantity = currentQuantity + change;

            // Ensure the quantity doesn't go below 1
            if (newQuantity >= 1) {
                quantityElement.value = newQuantity;
            }
        }
    </script>
</head>

<body>

    <?php include 'components/sidebar.php' ?>

    <div class="page-content">

        <?php include 'components/header.php'; ?>



        <div class="site-header-overlay">
            <img class="bg-image" src="img/home-appliances.jpg" alt="bg-img" id="parallax-image">
            <div class="overlay"></div>
            <h1 class="page-title">wishlist</h1>
            <div class="links">
                <a href="index.php"><span class="home">Discount Center</span></a>
                <span class="page-name"> > Wishlist</span>
            </div>
        </div>

        <section class="wishlist">
            <div class="row">
                <div class="column-left">
                    <div class="left-cart-wrapper">
                        <table class="left-cart-tbl">
                            <thead>
                                <tr>
                                    <th class="product-remove-header">&nbsp;</th>
                                    <th class="product-thumbnail-header">&nbsp;</th>
                                    <th class="product-name-header">product</th>
                                    <th class="cart-btn-header">&nbsp;</th>
                                    <th class="product-price-header">unit price</th>
                                    <th class="product-subtotal-header">stock status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE session_id = ?");
                                $select_wishlist->execute([$session_id]);
                                if ($select_wishlist->rowCount() > 0) {
                                    while ($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)) {
                                        $pid = $fetch_wishlist['pid'];
                                        $name = $fetch_wishlist['name'];
                                        $price = $fetch_wishlist['price'];
                                        $image = $fetch_wishlist['image'];
                                        $wishlist_id = $fetch_wishlist['id'];
                                ?>
                                        <tr class="cart-item">
                                            <td class="remove">
                                                <form action="" method="post">
                                                    <input type="hidden" name="wishlist_id" value="<?php echo $fetch_wishlist['id']; ?>">
                                                    <button type="submit" name="delete" class="delete-btn"><i class="fa-regular fa-trash-can"></i></button>
                                                </form>
                                            </td>
                                            <td class="product-thumbnail"><a href="product_view.php?pid=<?php echo $pid; ?>"><img src="uploaded_img/<?php echo $image; ?>" alt=""></a></td>
                                            <td class="product-name"><a href="product_view.php?pid=<?php echo $pid; ?>"><?php echo $name; ?></a></td>
                                            <td class="cart-btn">
                                                <form action="" method="post">
                                                    <input type="hidden" name="pid" value="<?php echo $pid ?>">
                                                    <input type="hidden" name="name" value="<?php echo $name; ?>">
                                                    <input type="hidden" name="price" value="<?php echo $price; ?>">
                                                    <input type="hidden" name="image" value="<?php echo $image; ?>">
                                                    <input type="hidden" name="qty" value="1">
                                                    <?php
                                                    $fetch_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                                                    $fetch_product->execute([$pid]);
                                                    $productData = $fetch_product->fetch(PDO::FETCH_ASSOC);

                                                    if ($productData['availability'] === 'out of stock') {
                                                        echo '<input type="hidden" value="out of stock">';
                                                    } else {
                                                        echo '<input type="submit" value="Add to cart" class="btn" name="add_to_cart">';
                                                    }
                                                    ?>
                                                </form>
                                            </td>
                                            <td class="price">Rs. <?php echo number_format($price); ?></td>
                                            <td class="status">
                                                <span style="color: <?= ($productData['availability'] === 'out of stock') ? 'red' : 'inherit'; ?>">
                                                    <?= $productData['availability'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="empty">Your wishlist is empty</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'components/footer.php'; ?>

    </div>


    <script src="js/script.js"></script>

</body>

</html>