<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['add_featured_product'])) {

    $heading = $_POST['heading'];
    $heading = filter_var($heading, FILTER_SANITIZE_STRING);
    $subheading = $_POST['subheading'];
    $subheading = filter_var($subheading, FILTER_SANITIZE_STRING);
    $category = $_POST['category_id'];
    $category = filter_var($category, FILTER_SANITIZE_NUMBER_INT);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../featured_img/' . $image;

    $select_image = $conn->prepare("SELECT * FROM `featured` WHERE image = ?");
    $select_image->execute([$image]);

    if ($select_image->rowCount() > 0) {
        $message[] = 'image already exists!';
    } else {

        $insert_featured = $conn->prepare("INSERT INTO `featured`(heading, subheading, category, image) VALUES(?,?,?,?)");
        $insert_featured->execute([$heading, $subheading, $category, $image]);

        if ($insert_featured) {
            if ($image_size > 2000000) {
                $message[] = 'image size is too large!';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'new featured product added!';
            }
        }
    }
};

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    $delete_featured_image = $conn->prepare("SELECT * FROM `featured` WHERE id = ?");
    $delete_featured_image->execute([$delete_id]);
    $fetch_delete_image = $delete_featured_image->fetch(PDO::FETCH_ASSOC);
    unlink('../featured_img/' . $fetch_delete_image['image']);
    $delete_featured = $conn->prepare("DELETE FROM `featured` WHERE id = ?");
    $delete_featured->execute([$delete_id]);
    header('location:add_featured.php');
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

        <h1 class="heading">add featured product</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="flex">
                <div class="inputBox">
                    <span>featured heading (required)</span>
                    <input type="text" class="box" required maxlength="100" placeholder="e.g : Latest Refrigerators" name="heading">
                </div>
                <div class="inputBox">
                    <span>featured subheading (required)</span>
                    <input type="text" class="box" required maxlength="100" placeholder="e.g : upto 50% off" name="subheading">
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
                    <span>featured product image (required)</span>
                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
                </div>
            </div>

            <input type="submit" value="add featured product" class="btn" name="add_featured_product">
        </form>

    </section>

    <section class="show-products">

        <h1 class="heading">featured products added</h1>

        <div class="box-container">

            <?php
            $select_featured = $conn->prepare("SELECT * FROM `featured`");
            $select_featured->execute();
            if ($select_featured->rowCount() > 0) {
                while ($fetch_featured = $select_featured->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <img src="../featured_img/<?= $fetch_featured['image']; ?>" alt="">
                        <div class="description"><?= $fetch_featured['subheading']; ?></div>
                        <div class="name"><?= $fetch_featured['heading']; ?></div>
                        <div class="flex-btn">
                            <a href="update_featured.php?update=<?= $fetch_featured['id']; ?>" class="option-btn">update</a>
                            <a href="add_featured.php?delete=<?= $fetch_featured['id']; ?>" class="delete-btn" onclick="return confirm('delete this featured product?');">delete</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no featured products added yet!</p>';
            }
            ?>

        </div>

    </section>

    <script src="../js/admin_script.js"></script>

</body>

</html>