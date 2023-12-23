<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
};

if (isset($_POST['add_category'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $parent_category = $_POST['category_id'];
   $parent_category = filter_var($parent_category, FILTER_SANITIZE_NUMBER_INT);

   // Check if the 'icon' key exists in the $_FILES array and if the upload was successful
   if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
      $icon = $_FILES['icon']['name'];
      $icon = filter_var($icon, FILTER_SANITIZE_STRING);
      $icon_size = $_FILES['icon']['size'];
      $icon_tmp_name = $_FILES['icon']['tmp_name'];
      $icon_folder = '../uploaded_img/' . $icon;
   } else {
      // Set a default value if 'icon' is not provided
      $icon = null;
   }

   $select_category = $conn->prepare("SELECT * FROM `categories` WHERE name = ?");
   $select_category->execute([$name]);

   if ($select_category->rowCount() > 0) {
      $message[] = 'category already exists!';
   } else {
      $insert_category = $conn->prepare("INSERT INTO `categories`(name, parent_id, icon) VALUES(?,?,?)");
      $insert_category->execute([$name, $parent_category, $icon]);

      if ($insert_category) {
         if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            if ($icon_size > 2000000) {
               $message[] = 'icon size is too large!';
            } else {
               move_uploaded_file($icon_tmp_name, $icon_folder);
               $message[] = 'new category added!';
            }
         } else {
            $message[] = 'new category added!';
         }
      }
   }
}


if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_category_icon = $conn->prepare("SELECT * FROM `categories` WHERE id = ?");
   $delete_category_icon->execute([$delete_id]);
   $fetch_delete_icon = $delete_category_icon->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_icon['icon']);
   $delete_category = $conn->prepare("DELETE FROM `categories` WHERE id = ?");
   $delete_category->execute([$delete_id]);
   header('location:categories.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>categories</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">

      <h1 class="heading">add category</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <span>category name (required)</span>
               <input type="text" class="box" required maxlength="100" placeholder="enter category name" name="name">
            </div>
            <div class="inputBox">
               <span>parent category</span>
               <select name="category_id" class="box" id="parentCategory">
                  <option value="0">-- main category --</option>
                  <?php
                  $select_categories = $conn->prepare("SELECT * FROM `categories`");
                  $select_categories->execute();
                  while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                     echo '<option value="' . $fetch_categories['id'] . '">' . $fetch_categories['name'] . '</option>';
                  }
                  ?>
               </select>
            </div>
            <div class="inputBox">
               <span>image (required)</span>
               <input type="file" name="icon" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" id="iconInput" required>
            </div>
         </div>

         <input type="submit" value="add category" class="btn" name="add_category">
      </form>

   </section>

   <section class="show-category">

      <h1 class="heading">main categories</h1>

      <div class="box-container">

         <?php
         $select_category = $conn->prepare("SELECT * FROM `categories` WHERE parent_id = 0");
         $select_category->execute();
         if ($select_category->rowCount() > 0) {
            while ($fetch_categories = $select_category->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="category-box">
                  <img src="../uploaded_img/<?= $fetch_categories['icon']; ?>" alt="">
                  <h3><?= $fetch_categories['name']; ?></h3>
                  <div class="flex-btn">
                     <a href="update_category.php?update=<?= $fetch_categories['id']; ?>" class="option-btn">update</a>
                     <a href="categories.php?delete=<?= $fetch_categories['id']; ?>" class="delete-btn" onclick="return confirm('delete this category?');">delete</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">no categories added yet!</p>';
         }
         ?>

      </div>

   </section>

   <section class="show-category">

      <h1 class="heading">sub categories</h1>

      <div class="box-container">

         <?php
         $select_category = $conn->prepare("SELECT * FROM `categories` WHERE parent_id != 0");
         $select_category->execute();
         if ($select_category->rowCount() > 0) {
            while ($fetch_categories = $select_category->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="category-box">
                  <h3><?= $fetch_categories['name']; ?></h3>
                  <div class="flex-btn">
                     <a href="update_category.php?update=<?= $fetch_categories['id']; ?>" class="option-btn">update</a>
                     <a href="categories.php?delete=<?= $fetch_categories['id']; ?>" class="delete-btn" onclick="return confirm('delete this category?');">delete</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">no categories added yet!</p>';
         }
         ?>

      </div>

   </section>

   <script src="../js/admin_script.js"></script>

   <script>
      document.getElementById('parentCategory').addEventListener('change', function() {
         var iconInput = document.getElementById('iconInput');
         // Check if the selected value is not an empty string (main category)
         if (this.value !== '0') {
            iconInput.disabled = true; // Disable the icon field
         } else {
            iconInput.disabled = false; // Enable the icon field
         }
      });
   </script>

</body>

</html>