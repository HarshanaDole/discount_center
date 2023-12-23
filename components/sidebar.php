<div class="sidebar">
    <i class="fa-solid fa-xmark"></i>
    <div class="search-bar">
        <input type="text" name="search-box" id="search-box" class="search-box" placeholder="Search for products" maxlength="100" required>
        <i class="fas fa-search"></i>
    </div>

    <div class="info-container">
        <div class="info-bar">
            <i class="fa-solid fa-headset"></i>
            <div class="info-text">
                <span class="info-subheading">Customer Support</span>
                <span class="info-heading">071 040 9000</span>
            </div>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <br>
        <div class="info-details">
            <div class="city">Bellanwila</div>
            <div class="number">071 040 9000</div>
            <div class="address">393/2, Dehiwala Road, Bellanwila</div>
            <div class="email">info@daedal.lk</div>
            <div class="icons">

            </div>
        </div>
    </div>

    <?php
    $select_main_categories = $conn->prepare("SELECT * FROM categories WHERE parent_id = 0");
    $select_main_categories->execute();
    $main_categories = $select_main_categories->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <ul class="expand">
        <li><a href="shop.php">Shop</a>
            <div class="more"><i class="fas fa-plus"></i></div>
        </li>
        <li class="cat"><a href="categories.php">Categories</a>
            <div class="more" onclick="toggleCategories(this)"><i class="fas fa-plus"></i></div>
        </li>
        <ul class="subcategories">
            <?php foreach ($main_categories as $main_category) : ?>
                <li class="subcat" data-category-id="<?php echo $main_category['id']; ?>">
                    <a href="#"><?php echo $main_category['name']; ?></a>
                    <?php
                    // Fetch subcategories for the current main category
                    $select_subcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = ?");
                    $select_subcategories->execute([$main_category['id']]);
                    $subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php if (!empty($subcategories)) : ?>
                        <div class="more" onclick="toggleSubCategories(this, <?php echo $main_category['id']; ?>)"><i class="fas fa-plus"></i></div>
                </li>
                <ul class="sub-subcategories" data-category-id="<?php echo $main_category['id']; ?>">
                    <?php foreach ($subcategories as $subcategory) : ?>
                        <li class="sub-subcat" data-subcategory-id="<?php echo $subcategory['id']; ?>">
                            <a href="#"><?php echo $subcategory['name']; ?></a>
                            <?php
                            // Fetch subcategories for the current main category
                            $select_subsubcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = ?");
                            $select_subsubcategories->execute([$subcategory['id']]);
                            $subsubcategories = $select_subsubcategories->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php if (!empty($subsubcategories)) : ?>
                                <div class="more" onclick="toggleSubSubCategories(this, <?php echo $subcategory['id']; ?>)"><i class="fas fa-plus"></i></div>
                        </li>
                        <ul class="sub-sub-subcategories" data-category-id="<?php echo $subcategory['id']; ?>">
                            <?php foreach ($subsubcategories as $subsubcategory) : ?>
                                <li class="sub-sub-subcat" data-subcategory-id="<?php echo $subsubcategory['id']; ?>">
                                    <a href="#"><?php echo $subsubcategory['name']; ?></a>
                                <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                </ul>
            <?php else : ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>
    </ul>

    <ul class="fixed">
        <li><a href="index.php">Home</a></li>
        <li><a href="service.php">Service</a></li>
        <li><a href="about.php">About</a></li>
    </ul>

    <ul class="fixed">
        <li><i class="fas fa-heart"></i><a href="#">Wishlist</a></li>
        <li><i class="fa-solid fa-cart-shopping"></i><a href="#">Cart</a></li>
    </ul>

    <ul class="socials">
        <li class="facebook"><i class="fa-brands fa-facebook-f"></i></li>
        <li class="insta"><i class="fa-brands fa-instagram"></i></li>
        <li class="tiktok"><i class="fa-brands fa-tiktok"></i></li>
    </ul>
</div>