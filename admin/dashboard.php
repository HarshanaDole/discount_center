<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

if (isset($_SESSION['admin_id'])) {
   $admin_id = $_SESSION['admin_id'];
} else {
   header('location:admin_login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="dashboard">

      <h1 class="heading">dashboard</h1>

      <div class="box-container">

         <div class="box">
            <?php
            $admin_id = $_SESSION['admin_id'];
            $select_admin = $conn->prepare("SELECT name FROM `admins` WHERE id = ?");
            $select_admin->execute([$admin_id]);
            $admin_name = $select_admin->fetchColumn();
            ?>
            <h3>Welcome</h3>
            <p><?= $admin_name ?></p>
            <a href="update_profile.php" class="btn">update profile</a>
         </div>

         <div class="box">
            <?php
            $total_processing = 0;
            $select_processing = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_processing->execute(['processing']);
            if ($select_processing->rowCount() > 0) {
               while ($fetch_processing = $select_processing->fetch(PDO::FETCH_ASSOC)) {
                  $total_processing += $fetch_processing['total_price'];
               }
            }
            ?>
            <h3><span>$</span><?= $total_processing; ?></h3>
            <p>total processing</p>
            <a href="placed_orders.php?payment_status=processing" class="btn">see orders</a>
         </div>

         <div class="box">
            <?php
            $total_ready = 0;
            $select_ready = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_ready->execute(['ready']);
            if ($select_ready->rowCount() > 0) {
               while ($fetch_ready = $select_ready->fetch(PDO::FETCH_ASSOC)) {
                  $total_ready += $fetch_ready['total_price'];
               }
            }
            ?>
            <h3><span>$</span><?= $total_ready; ?></h3>
            <p>total ready</p>
            <a href="placed_orders.php?payment_status=ready" class="btn">see orders</a>
         </div>

         <div class="box">
            <?php
            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_completes->execute(['completed']);
            if ($select_completes->rowCount() > 0) {
               while ($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)) {
                  $total_completes += $fetch_completes['total_price'];
               }
            }
            ?>
            <h3><span>$</span><?= $total_completes; ?></h3>
            <p>completed orders</p>
            <a href="placed_orders.php?payment_status=completed" class="btn">see orders</a>
         </div>

         <div class="box">
            <span class="notification">0</span>
            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount()
            ?>
            <h3><?= $number_of_orders; ?></h3>
            <p>orders placed</p>
            <a href="placed_orders.php" class="btn">see orders</a>
         </div>

         <div class="box">
            <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            $number_of_products = $select_products->rowCount()
            ?>
            <h3><?= $number_of_products; ?></h3>
            <p>products added</p>
            <a href="products.php" class="btn">see products</a>
         </div>

         <div class="box">
            <?php
            $select_category = $conn->prepare("SELECT * FROM `categories`");
            $select_category->execute();
            $number_of_categories = $select_category->rowCount()
            ?>
            <h3><?= $number_of_categories; ?></h3>
            <p>categories added</p>
            <a href="categories.php" class="btn">see categories</a>
         </div>

         <div class="box">
            <?php
            $select_featured = $conn->prepare("SELECT * FROM `featured`");
            $select_featured->execute();
            $number_of_featured = $select_featured->rowCount()
            ?>
            <h3><?= $number_of_featured; ?></h3>
            <p>featured products</p>
            <a href="add_featured.php" class="btn">see featured</a>
         </div>

         <div class="box">
            <?php
            $select_admins = $conn->prepare("SELECT * FROM `admins`");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount()
            ?>
            <h3><?= $number_of_admins; ?></h3>
            <p>administrators</p>
            <a href="admin_accounts.php" class="btn">see admins</a>
         </div>

         <div class="box">
            <?php
            $select_messages = $conn->prepare("SELECT * FROM `messages`");
            $select_messages->execute();
            $number_of_messages = $select_messages->rowCount()
            ?>
            <h3><?= $number_of_messages; ?></h3>
            <p>new messages</p>
            <a href="messages.php" class="btn">see messages</a>
         </div>

      </div>

   </section>

   <script src="../js/admin_script.js"></script>

   <script>
      function fetchNewOrderCount() {
         fetch('../components/get_new_order_count.php')
            .then(response => response.json())
            .then(data => {
               const notificationBadge2 = document.querySelector('.box .notification');


               if (data.newOrderCount > 0) {
                  notificationBadge2.textContent = data.newOrderCount;
                  notificationBadge2.style.display = 'inline';
               } else {
                  notificationBadge2.style.display = 'none';
               }
            })
            .catch(error => {
               console.error('Error fetching new order count:', error);
            });
      }

      fetchNewOrderCount();

      setInterval(fetchNewOrderCount, 3000);
   </script>

</body>

</html>