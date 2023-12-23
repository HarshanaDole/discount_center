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

        <section class="product-view">
            <div class="row">
                <div class="image-container">
                    <img src="img/360moto-01.jpg" alt="">
                    <div class="other-img">
                        <img src="img/360moto-01.jpg" alt="">
                        <img src="img/360moto-01.jpg" alt="">
                        <img src="img/360moto-01.jpg" alt="">
                    </div>
                </div>
                <div class="product-info">
                    <h1 class="product-name">Motorola Moto 360</h1>
                    <span class="price">Rs. 14,000</span>
                    <div class="description">The new Moto 360 combines edge-to-edge glass with an exceptionally thin, polished bezel, giving you the largest viewing area. Whether you choose rose gold, black, or silver, the case is precision-crafted from aircraft-grade stainless steel.</div>
                    <div class="button-container">
                        <div class="quantity" id="quantity">1</div>
                        <div class="adjust">
                            <button class="up" onclick="updateQuantity(1)">+</button>
                            <button class="down" onclick="updateQuantity(-1)">-</button>
                        </div>
                        <div class="btn">add to cart</div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="product-meta">
                    <span class="model-wrapper">model: <span class="model">SKU1234</span></span>
                    <span class="categories-wrapper">categories: <span class="categories">Accessories, </span><span class="categories">Headphones</span></span>
                </div>
            </div>
        </section>

        <?php include 'components/footer.php'; ?>

    </div>


    <script src="js/script.js"></script>


    <script>
        // Function to update the quantity
        function updateQuantity(change) {
            var quantityElement = document.getElementById("quantity");
            var currentQuantity = parseInt(quantityElement.textContent);

            // Update the quantity based on the change parameter
            var newQuantity = currentQuantity + change;

            // Ensure the quantity doesn't go below 1
            if (newQuantity >= 1) {
                quantityElement.textContent = newQuantity;
            }

            // Always show the adjust field after updating the quantity
            showAdjust();
        }

        // Function to show the adjust field
        function showAdjust() {
            var adjustField = document.querySelector(".adjust");
            adjustField.style.display = "flex";
        }
    </script>



</body>