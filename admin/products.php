<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
};

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $model = $_POST['model'];
   $model = filter_var($model, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category_id'];
   $category = filter_var($category, FILTER_SANITIZE_NUMBER_INT);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   $point_desc = $_POST['point_desc'];
   $point_desc = filter_var($point_desc, FILTER_SANITIZE_STRING);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   if (empty($image_02)) {
      $image_02 = $image_01;
      $image_folder_02 = $image_folder_01;
   }

   if (empty($image_03)) {
      $image_03 = $image_01;
      $image_folder_03 = $image_folder_01;
   }

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'product name already exist!';
   } else {

      $insert_products = $conn->prepare("INSERT INTO `products`(name, model, description, point_desc, price, image_01, image_02, image_03, category) VALUES(?,?,?,?,?,?,?,?,?)");
      $insert_products->execute([$name, $model, $description, $point_desc, $price, $image_01, $image_02, $image_03, $category]);

      if ($insert_products) {
         if ($image_size_01 > 2000000 or $image_size_02 > 2000000 or $image_size_03 > 2000000) {
            $message[] = 'image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'new product added!';
         }
      }
   }
};

if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   if (!empty($fetch_delete_image['image_01']) && file_exists('../uploaded_img/' . $fetch_delete_image['image_01'])) {
      unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   }

   if (!empty($fetch_delete_image['image_02']) && file_exists('../uploaded_img/' . $fetch_delete_image['image_02'])) {
      unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   }

   if (!empty($fetch_delete_image['image_03']) && file_exists('../uploaded_img/' . $fetch_delete_image['image_03'])) {
      unlink('../uploaded_img/' . $fetch_delete_image['image_03']);
   }
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   header('location:products.php');
}

if (isset($_POST['add_to_featured'])) {
   $pid = $_POST['pid'];

   $updateFeatured = $conn->prepare("UPDATE `products` SET featured = NOT featured WHERE id = ?");
   $updateFeatured->execute([$pid]);

   header('location:products.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">

      <h1 class="heading">add product</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <span>product name (required)</span>
               <input type="text" class="box" required maxlength="100" placeholder="enter product name" name="name">
            </div>
            <div class="inputBox">
               <span>model number (required)</span>
               <input type="text" class="box" required maxlength="50" placeholder="enter model number" name="model">
            </div>
            <div class="inputBox">
               <span>product price (required)</span>
               <input type="text" name="price" placeholder="enter product price" class="box no-spinner" onkeypress="if(this.value.length == 10) return false;" inputmode="numeric" pattern="\d+(\.\d{2})?" required>
            </div>
            <div class="inputBox">
               <span>image 01 (required)</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
            </div>
            <div class="inputBox">
               <span>image 02</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>
            <div class="inputBox">
               <span>image 03</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>
            <div class="inputBox">
               <span>category (required)</span>
               <select name="category_id" class="box" required>
                  <option value="">-- select category --</option>
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
               <span>product description (required)</span>
               <textarea name="description" placeholder="enter product description" class="box" required maxlength="500" cols="30" rows="10"></textarea>
            </div>
            <div class="inputBox">
               <span>product description (point-wise)</span>
               <textarea name="point_desc" placeholder="press enter to add pointer" class="box" maxlength="500" cols="30" rows="10"></textarea>
            </div>
         </div>

         <input type="submit" value="add product" class="btn" name="add_product">
      </form>

   </section>

   <?php
   $select_products = $conn->prepare("SELECT * FROM `products`");
   $select_products->execute();
   $products = []; // Initialize an empty array to store the products

   if ($select_products->rowCount() > 0) {
      while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
         $products[] = $fetch_products; // Store each product in the array
      }

      // Function to sort products by ID in descending order (high to low)
      function sortByIdDescending($a, $b)
      {
         return $b['id'] - $a['id'];
      }

      // Sort the products array using the custom sort function
      usort($products, 'sortByIdDescending');
   } else {
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   <section class="show-products">

      <h1 class="heading">products added</h1>

      <div class="filter-container">

         <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by product or price..">
            <button onclick="searchProducts()">Search</button>
         </div>

         <div class="filter-dropdown">
            <label for="filterOptions">Sort By:</label>
            <select id="filterOptions" onchange="applyFilters()">
               <option value="latest">Latest</option>
               <option value="lowToHigh">Price (low to high)</option>
               <option value="highToLow">Price (high to low)</option>
               <option value="AtoZ">A-Z</option>
               <option value="ZtoA">Z-A</option>
               <option value="featured">Featured</option>
            </select>
         </div>

      </div>

      <div class="box-container">

         <?php foreach ($products as $fetch_products) : ?>

            <div class="box <?php echo ($fetch_products['availability'] === 'out of stock') ? 'out-of-stock' : ''; ?><?php echo ($fetch_products['featured'] == 1) ? ' featured' : ''; ?>">
               <form method="post" action="">
                  <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                  <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
                  <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                  <input type="hidden" name="image" value="<?= $fetch_products['image_01']; ?>">
                  <?php if ($fetch_products['featured'] == 1) : ?>
                     <button class="fa-solid fa-xmark" type="submit" name="add_to_featured"></button>
                  <?php else : ?>
                     <button class="fas fa-heart" type="submit" name="add_to_featured"></button>
                  <?php endif; ?>
               </form>
               <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
               <div class="name"><?= $fetch_products['name']; ?></div>
               <?php
               $formattedPrice = number_format($fetch_products['price']);
               ?>
               <div class="price">Rs. <span><?= $formattedPrice; ?></span></div>
               <div class="description"><span><?= $fetch_products['description']; ?></span></div>
               <div class="flex-btn">
                  <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
                  <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
               </div>
            </div>

         <?php endforeach; ?>
      </div>

   </section>

   <script src="../js/admin_script.js"></script>

   <script>
      function searchProducts() {
         console.log("Search button clicked!");
         const searchInput = document.getElementById("searchInput");
         const searchTerm = searchInput.value.trim().toLowerCase();

         const productBoxes = document.querySelectorAll(".box");
         productBoxes.forEach(box => {
            const nameElement = box.querySelector(".name");
            const priceElement = box.querySelector(".price span");

            if (nameElement && priceElement) {
               const name = nameElement.textContent.toLowerCase();
               const price = priceElement.textContent.toLowerCase();

               if (name.includes(searchTerm) || price.includes(searchTerm)) {
                  box.style.display = "block";
               } else {
                  box.style.display = "none";
               }
            }
         });
      }

      var productBoxes = document.querySelectorAll(".box-container .box");

      function applyFilters() {
         var selectedOption = document.getElementById("filterOptions").value;
         var sortedBoxes;

         switch (selectedOption) {
            case "lowToHigh":
               sortedBoxes = Array.from(productBoxes).sort((a, b) => {
                  var priceA = parseFloat(a.querySelector(".price span").textContent.replace("Rs. ", "").replace(",", ""));
                  var priceB = parseFloat(b.querySelector(".price span").textContent.replace("Rs. ", "").replace(",", ""));
                  return priceA - priceB;
               });
               break;
            case "highToLow":
               sortedBoxes = Array.from(productBoxes).sort((a, b) => {
                  var priceA = parseFloat(a.querySelector(".price span").textContent.replace("Rs. ", "").replace(",", ""));
                  var priceB = parseFloat(b.querySelector(".price span").textContent.replace("Rs. ", "").replace(",", ""));
                  return priceB - priceA;
               });
               break;
            case "AtoZ":
               sortedBoxes = Array.from(productBoxes).sort((a, b) => {
                  var nameA = a.querySelector(".name").textContent.toUpperCase();
                  var nameB = b.querySelector(".name").textContent.toUpperCase();
                  return nameA.localeCompare(nameB);
               });
               break;
            case "ZtoA":
               sortedBoxes = Array.from(productBoxes).sort((a, b) => {
                  var nameA = a.querySelector(".name").textContent.toUpperCase();
                  var nameB = b.querySelector(".name").textContent.toUpperCase();
                  return nameB.localeCompare(nameA);
               });
               break;
            case "featured":
               sortedBoxes = Array.from(productBoxes).filter(box => {
                  return box.classList.contains("featured");
               });
               break;
            case "latest":
            default:
               sortedBoxes = Array.from(productBoxes).sort((a, b) => {
                  var idA = parseInt(a.querySelector("input[name='pid']").value);
                  var idB = parseInt(b.querySelector("input[name='pid']").value);
                  return idB - idA;
               });
         }

         // Append the sorted boxes back to the container
         var boxContainer = document.querySelector(".box-container");
         boxContainer.innerHTML = '';
         sortedBoxes.forEach(box => {
            boxContainer.appendChild(box);
         });
      }
   </script>

</body>

</html>