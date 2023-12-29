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
    $cart_id = $_POST['cart_id'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->execute([$cart_id]);
    header('Location: cart.php');
    exit();
}

if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cartId => $newQuantity) {
        $updateQty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
        $updateQty->execute([$newQuantity, $cartId]);
    }
    $message[] = 'Cart quantities updated';
    header('Location: cart.php');
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
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
            <h1 class="page-title">cart</h1>
            <div class="links">
                <a href="index.php"><span class="home">Discount Center</span></a>
                <span class="page-name"> > Cart</span>
            </div>
        </div>

        <section class="cart">
            <?php 
            $grand_total = 0;
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE session_id = ?");
            $select_cart->execute([$session_id]);
            if ($select_cart->rowCount() > 0) {
            ?>
            <form action="" method="post">
                <div class="row">
                    <div class="column-left">
                        <div class="left-cart-wrapper">
                            <table class="left-cart-tbl">
                                <thead>
                                    <tr>
                                        <th class="product-thumbnail-header">&nbsp;</th>
                                        <th class="product-name-header">product</th>
                                        <th class="product-price-header">&nbsp;</th>
                                        <th class="product-qty-header">qty</th>
                                        <th class="product-subtotal-header">subtotal</th>
                                        <th class="product-remove-header">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                            $pid = $fetch_cart['pid'];
                                            $name = $fetch_cart['name'];
                                            $price = $fetch_cart['price'];
                                            $image = $fetch_cart['image'];
                                            $quantity = $fetch_cart['quantity'];
                                            $subtotal = $price * $quantity;
                                            $grand_total += $subtotal;
                                    ?>
                                            <tr class="cart-item">
                                                <td class="product-thumbnail"><a href="product_view.php?pid=<?php echo $pid; ?>"><img src="uploaded_img/<?php echo $image; ?>" alt=""></a></td>
                                                <td class="product-name"><a href="product_view.php?pid=<?php echo $pid; ?>"><?php echo $name; ?></a></td>
                                                <td class="price">Rs. <?php echo number_format($price); ?></td>
                                                <td class="qty">
                                                    <div class="qty-container">
                                                        <input type="number" name="quantity[<?php echo $fetch_cart['id']; ?>]" class="quantity" id="quantity_<?php echo $pid; ?>" value="<?php echo $quantity; ?>" min="1" max="99" onclick="event.preventDefault()" onkeypress="if(this.value.length == 2) return false;">
                                                        <div class="adjust" id="adjust_<?php echo $pid; ?>">
                                                            <button class="up" onclick="updateQuantity(<?php echo $pid; ?>, 1); event.preventDefault()">+</button>
                                                            <button class="down" onclick="updateQuantity(<?php echo $pid; ?>, -1); event.preventDefault()">-</button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="subtotal">Rs. <?php echo number_format($subtotal); ?></td>
                                                <td class="remove">
                                                    <form action="" method="post">
                                                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                                                        <button type="submit" name="delete" class="delete-btn"><i class="fa-regular fa-trash-can"></i></button>
                                                    </form>
                                                </td>
                                            <?php
                                        }
                                            ?>
                                            </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="column">
                        <div class="right-cart-wrapper">
                            <div class="cart-totals-container">
                                <h2>cart totals</h2>
                                <table class="right-cart-tbl">
                                    <tbody>
                                        <tr>
                                            <th class="product-subtotal-header">subtotal</th>
                                            <td class="product-subtotal">Rs. <?= number_format($grand_total) ?></th>
                                        </tr>
                                        <tr>
                                            <th class="product-total-header">total</th>
                                            <td class="product-total">Rs. <?= number_format($grand_total) ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="cart-buttons">
                                    <input type="submit" class="update-cart-btn" name="update_cart" value="Update Cart">
                                    <a href="" class="checkout-btn">proceed to checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php
                                    } else {
                                        echo '
            <div class="shopping-img">
                <span class="material-symbols-outlined">
                    shopping_bag
                </span>
            </div>
            <div class="empty">Your cart is currently empty</div>
            <div class="btn-container"><a href="index.php"><button class="btn" type="submit">Return to Home</button></a></div>';
                                    }
        ?>
        </section>


        <?php include 'components/footer.php'; ?>

    </div>


    <script src="js/script.js"></script>

</body>

</html>