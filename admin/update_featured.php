<?php

include '../components/connect.php';

include '../components/check_inactivity.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $heading = $_POST['heading'];
    $heading = filter_var($heading, FILTER_SANITIZE_STRING);
    $subheading = $_POST['subheading'];
    $subheading = filter_var($subheading, FILTER_SANITIZE_STRING);
    $category = $_POST['category_id'];
    $category = filter_var($category, FILTER_SANITIZE_NUMBER_INT);

    $update_featured = $conn->prepare("UPDATE `featured` SET heading = ?, subheading = ?, category = ? WHERE id = ?");
    $update_featured->execute([$heading, $subheading, $category, $id]);

    $message[] = 'featured product updated successfully!';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../featured_img/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'icon size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `featured` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('../featured_img/' . $old_image);
            $message[] = 'image updated successfully!';
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

        <h1 class="heading">update featured product</h1>

        <?php
        $update_id = $_GET['update'];
        $select_featured = $conn->prepare("SELECT * FROM `featured` WHERE id = ?");
        $select_featured->execute([$update_id]);
        if ($select_featured->rowCount() > 0) {
            while ($fetch_featured = $select_featured->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $fetch_featured['id']; ?>">
                    <input type="hidden" name="old_image" value="<?= $fetch_featured['image']; ?>">
                    <div class="image-container">
                        <div class="main-image">
                            <img src="../featured_img/<?= $fetch_featured['image']; ?>" alt="">
                        </div>
                    </div>
                    <span>update heading</span>
                    <input type="text" name="heading" required class="box" maxlength="100" placeholder="enter heading" value="<?= $fetch_featured['heading']; ?>">
                    <span>update subheading</span>
                    <input type="text" name="subheading" required class="box" maxlength="100" placeholder="enter subheading" value="<?= $fetch_featured['subheading']; ?>">
                    <span>update category</span>
                    <select name="category_id" required class="box">
                        <?php
                        $select_categories = $conn->prepare("SELECT * FROM categories");
                        $select_categories->execute();
                        while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($fetch_categories['id'] == $fetch_featured['category']) ? 'selected' : '';
                            echo "<option value='{$fetch_categories['id']}' $selected>{$fetch_categories['name']}</option>";
                        }
                        ?>
                    </select>
                    <span>update image</span>
                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
                    <div class="flex-btn">
                        <input type="submit" name="update" class="btn" value="update">
                        <a href="add_featured.php" class="option-btn">go back</a>
                    </div>
                </form>

        <?php
            }
        } else {
            echo '<p class="empty">no featured product found!</p>';
        }
        ?>

    </section>


    <script src="../js/admin_script.js"></script>

</body>

</html>