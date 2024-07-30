<?php

include 'components/connect.php'

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
        <?php include 'components/sidebar.php'; ?>
        <div class="page-content">
            <?php include 'components/header.php'; ?>
            
            <div class="site-header-overlay">
                <img class="bg-image" src="img/home-appliances.jpg" alt="bg-img" id="parallax-image">
                <div class="overlay"></div>
                <h1 class="page-title">Checkout</h1>
                <div class="links">
                    <a href="index.php"><span class="home">Discount Center</span></a>
                    <span class="page-name"> > Checkout</span>
                </div>
            </div>

            <section class="checkout">
                <div class="coupon-section">
                    <div class="coupon-container">
                        <p>Have a coupon? <a href="#" id="showCoupon">Click here to enter your code</a></p>
                        <form id="couponForm" style="display: none;">
                            <input type="text" placeholder="Coupon code" class="coupon-input">
                            <button type="submit" class="coupon-button">Apply Coupon</button>
                        </form>
                    </div>
                </div>

                <div class="checkout-container">
                    <div class="billing-details">
                        <h2>Billing Details</h2>
                        <form>
                            <div class="form-group">
                                <label for="first-name">First Name *</label>
                                <input type="text" id="first-name" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name *</label>
                                <input type="text" id="last-name" required>
                            </div>
                            <div class="form-group">
                                <label for="company-name">Company Name (Optional)</label>
                                <input type="text" id="company-name">
                            </div>
                            <div class="form-group">
                                <label for="country-region">Country / Region *</label>
                                <select id="country-region" required>
                                    <option value="us">United States (US)</option>
                                    <option value="sl">Sri Lanka (SL)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="street-address">Street Address *</label>
                                <input type="text" id="street-address" placeholder="House number and street name" required>
                                <input type="text" id="apartment" placeholder="Apartment, suite, unit, etc. (Optional)">
                            </div>
                            <div class="form-group">
                                <label for="town-city">Town / City *</label>
                                <input type="text" id="town-city" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State *</label>
                                <input type="text" id="state" requied>
                            </div>
                            <div class="form-group">
                                <label for="zip">ZIP *</label>
                                <input type="text" id="zip" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" required>
                            </div>
                        </form>
                    </div>
                    <div class="order-details">
                        <h2>Your Order</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>LG Refrigerator GBB306PZ</td>
                                    <td>Rs. 89,000.00</td>
                                </tr>
                                <tr>
                                    <td>Subtotal</td>
                                    <td>Rs. 89,000.00</td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td>
                                        <input type="radio" name="shipping" id="flat-rate" checked>
                                        <label for="flat-rate">Flat Rate: Rs. 500.00</label>
                                        <br>
                                        <input type="radio" name="shipping" id="free-shipping">
                                        <label for="free-shipping">Free Shipping</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>Rs. 89,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="payment-method">
                            <input type="radio" name="payment" id="direct-bank-transfer" checked>
                            <label for="direct-bank-transfer">Direct Bank Transfer</label>
                            <p>Make your payment directly into our bank account. Please use your order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.</p>
                            <br>
                            <input type="radio" name="payment" id="paypal">
                            <label for="paypal">Paypal</label>
                            <p>Pay via Paypal; you can pay with your credit card if you don't have a Paypal account.</p>
                        </div>
                        <button type="submit">Place Order</button>
                    </div>
                </div>
            </section>
        </div>

        <script src="js/script.js"></script>
        <script>
            document.getElementById('showCoupon').addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('couponForm').style.display = 'block';
            });
        </script>
    </body>
</html>