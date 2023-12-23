<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['update'])) {

   $id = $_POST['id'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $parent_category = $_POST['category_id'];
   $parent_category = filter_var($parent_category, FILTER_SANITIZE_NUMBER_INT);

   $update_category = $conn->prepare("UPDATE `categories` SET name = ?, parent_id = ? WHERE id = ?");
   $update_category->execute([$name, $parent_category, $id]);

   $message[] = 'category updated successfully!';

   $old_icon = $_POST['old_icon'];
   $icon = $_FILES['icon']['name'];
   $icon = filter_var($icon, FILTER_SANITIZE_STRING);
   $icon_size = $_FILES['icon']['size'];
   $icon_tmp_name = $_FILES['icon']['tmp_name'];
   $icon_folder = '../uploaded_img/' . $icon;

   if (!empty($icon)) {
      if ($icon_size > 2000000) {
         $message[] = 'icon size is too large!';
      } else {
         $update_icon = $conn->prepare("UPDATE `categories` SET icon = ? WHERE id = ?");
         $update_icon->execute([$icon, $id]);
         move_uploaded_file($icon_tmp_name, $icon_folder);
         unlink('../uploaded_img/' . $old_icon);
         $message[] = 'icon updated successfully!';
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
   <title>update category</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="update-product">

      <h1 class="heading">update category</h1>

      <?php
      $update_id = $_GET['update'];
      $select_category = $conn->prepare("SELECT * FROM `categories` WHERE id = ?");
      $select_category->execute([$update_id]);
      if ($select_category->rowCount() > 0) {
         while ($fetch_categories = $select_category->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="id" value="<?= $fetch_categories['id']; ?>">
               <input type="hidden" name="parent_id" value="<?= $currentParentId ?>">
               <input type="hidden" name="old_icon" value="<?= $fetch_categories['icon']; ?>">
               <div class="image-container">
                  <div class="main-image">
                     <img src="../uploaded_img/<?= $fetch_categories['icon']; ?>" alt="">
                  </div>
               </div>
               <span>update name</span>
               <input type="text" name="name" required class="box" maxlength="100" placeholder="enter category name" value="<?= $fetch_categories['name']; ?>">
               <span>update parent category</span>
               <select name="category_id" required class="box">
                  <option value="0">-- main category --</option>
                  <?php
                  $select_categories = $conn->prepare("SELECT * FROM categories");
                  $currentParentId = $fetch_categories['parent_id'];
                  $select_categories->execute();
                  while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                     $selected = ($fetch_categories['id'] == $currentParentId) ? 'selected' : '';
                     echo "<option value='{$fetch_categories['id']}' $selected>{$fetch_categories['name']}</option>";
                  }
                  ?>
               </select>
               <span>update icon</span>
               <input type="file" name="icon" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
               <div class="flex-btn">
                  <input type="submit" name="update" class="btn" value="update">
                  <a href="categories.php" class="option-btn">go back</a>
               </div>
            </form>

      <?php
         }
      } else {
         echo '<p class="empty">no categories found!</p>';
      }
      ?>

   </section>


   <script src="../js/admin_script.js"></script>

</body>

</html>