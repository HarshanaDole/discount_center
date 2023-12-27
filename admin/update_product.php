<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['update'])) {

   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $model = $_POST['model'];
   $model = filter_var($model, FILTER_SANITIZE_STRING);
   $old_price = $_POST['old_price'];
   $old_price = filter_var($old_price, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category_id'];
   $category = filter_var($category, FILTER_SANITIZE_NUMBER_INT);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $point_desc = $_POST['point_desc'];
   $point_desc = filter_var($point_desc, FILTER_SANITIZE_STRING);
   $availability = $_POST['availability'];
   $availability = filter_var($availability, FILTER_SANITIZE_STRING);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, model = ?, old_price = ?, price = ?, category = ?, description = ?, point_desc = ?, availability = ? WHERE id = ?");
   $update_product->execute([$name, $model, $old_price, $price, $category, $description, $point_desc, $availability, $pid]);

   $message[] = 'product updated successfully!';

   $old_image_01 = $_POST['old_image_01'];
   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   if (!empty($_FILES['image_01']['name'])) {
      if ($image_size_01 > 2000000) {
         $message[] = 'image size is too large!';
      } else {
         $update_image_01 = $conn->prepare("UPDATE `products` SET image_01 = ? WHERE id = ?");
         $update_image_01->execute([$image_01, $pid]);
         move_uploaded_file($image_tmp_name_01, $image_folder_01);
         if (file_exists('../uploaded_img/' . $old_image_01) && $old_image_01 !== $image_01) {
            unlink('../uploaded_img/' . $old_image_01);
         }
         $message[] = 'image 01 updated successfully!';
      }
   }

   $old_image_02 = $_POST['old_image_02'];
   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   if (!empty($_FILES['image_02']['name'])) {
      if ($image_size_02 > 2000000) {
         $message[] = 'image size is too large!';
      } else {
         $update_image_02 = $conn->prepare("UPDATE `products` SET image_02 = ? WHERE id = ?");
         $update_image_02->execute([$image_02, $pid]);
         move_uploaded_file($image_tmp_name_02, $image_folder_02);
         if (file_exists('../uploaded_img/' . $old_image_02) && $old_image_02 !== $image_02) {
            unlink('../uploaded_img/' . $old_image_02);
         }
         $message[] = 'image 02 updated successfully!';
      }
   }

   $old_image_03 = $_POST['old_image_03'];
   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   if (!empty($_FILES['image_03']['name'])) {
      if ($image_size_03 > 2000000) {
         $message[] = 'image size is too large!';
      } else {
         $update_image_03 = $conn->prepare("UPDATE `products` SET image_03 = ? WHERE id = ?");
         $update_image_03->execute([$image_03, $pid]);
         move_uploaded_file($image_tmp_name_03, $image_folder_03);
         if (file_exists('../uploaded_img/' . $old_image_03) && $old_image_03 !== $image_03) {
            unlink('../uploaded_img/' . $old_image_03);
         }
         $message[] = 'image 03 updated successfully!';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update product</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="update-product">

      <h1 class="heading">update product</h1>

      <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if ($select_products->rowCount() > 0) {
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
               <input type="hidden" name="old_image_02" value="<?= $fetch_products['image_02']; ?>">
               <input type="hidden" name="old_image_03" value="<?= $fetch_products['image_03']; ?>">
               <div class="image-container">
                  <div class="main-image">
                     <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                  </div>
                  <div class="sub-image">
                     <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                     <img src="../uploaded_img/<?= $fetch_products['image_02']; ?>" alt="">
                     <img src="../uploaded_img/<?= $fetch_products['image_03']; ?>" alt="">
                  </div>
               </div>
               <span>update name</span>
               <input type="text" name="name" required class="box" maxlength="100" placeholder="enter product name" value="<?= $fetch_products['name']; ?>">
               <span>update model</span>
               <input type="text" name="model" required class="box" maxlength="50" placeholder="enter model number" value="<?= $fetch_products['model']; ?>">
               <span>update old price</span>
               <input type="text" name="old_price" required class="box no-spinner" placeholder="enter old price" onkeypress="if(this.value.length == 10) return false;" pattern="\d+(\.\d{2})?" value="<?= $fetch_products['old_price']; ?>">
               <span>update price</span>
               <input type="text" name="price" required class="box no-spinner" placeholder="enter product price" onkeypress="if(this.value.length == 10) return false;" pattern="\d+(\.\d{2})?" value="<?= $fetch_products['price']; ?>">
               <span>update category</span>
               <select name="category_id" required class="box">
                  <?php
                  $select_categories = $conn->prepare("SELECT * FROM categories");
                  $select_categories->execute();
                  while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                     $selected = ($fetch_categories['id'] == $fetch_products['category']) ? 'selected' : '';
                     echo "<option value='{$fetch_categories['id']}' $selected>{$fetch_categories['name']}</option>";
                  }
                  ?>
               </select>
               <span>update description</span>
               <textarea name="description" class="box" required cols="30" rows="10"><?= $fetch_products['description']; ?></textarea>
               <span>update point description</span>
               <textarea name="point_desc" class="box" cols="30" rows="10"><?= $fetch_products['point_desc']; ?></textarea>
               <span>update image 01</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <span>update image 02</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <span>update image 03</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <span>update availability</span>
               <select name="availability" class="box">
                  <?php
                  $availabilityOptions = array("in stock", "out of stock");

                  foreach ($availabilityOptions as $option) {
                     $selected = ($option == $fetch_products['availability']) ? 'selected' : '';
                     echo "<option value='$option' $selected>$option</option>";
                  }
                  ?>
               </select>
               <div class="flex-btn">
                  <input type="submit" name="update" class="btn" value="update">
                  <a href="products.php" class="option-btn">go back</a>
               </div>
            </form>

      <?php
         }
      } else {
         echo '<p class="empty">no product found!</p>';
      }
      ?>

   </section>


   <script src="../js/admin_script.js"></script>

</body>

</html>