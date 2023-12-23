<header class="header">

    <section class="flex">

        <a href="index.php" class="logo"><img src="img/dclogocrop.jpg" alt="DC Logo"></a>

        <div class="right-items">

            <div class="menu-btn">
                <i class="fas fa-bars"></i>
                <span class="menu-heading">menu</span>
            </div>
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
                <div class="info-details">
                    <div class="city">Bellanwila</div>
                    <div class="number">071 040 9000</div>
                    <div class="address">393/2, Dehiwala Road, Bellanwila</div>
                    <div class="email">info@daedal.lk</div>
                    <div class="icons">

                    </div>
                </div>
            </div>


            <div class="cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <div class="cart-info">
                    <span class="subheading">0 items</span>
                    <span class="heading">0.00LKR</span>
                </div>
                <div class="cart-notification">0</div>
            </div>

        </div>

    </section>

    <hr style="border-top: 1px solid whitesmoke;">


    <nav class="navbar">
        <ul>
            <li><a href="index.php">Home</a></li>

            <li><a href="#">Shop<i class="fa-solid fa-chevron-down"></i></a>
                <div class="subnav">
                    <ul>
                        <li><a href="#"><i class="fa-solid fa-chevron-right"></i> Cart</a></li>
                        <li><a href="#"><i class="fa-solid fa-chevron-right"></i> Wishlist</a></li>
                        <li><a href="#"><i class="fa-solid fa-chevron-right"></i> My account</a></li>
                    </ul>
                </div>
            </li>
            <?php
            // Assuming you have a database connection
            // $conn = new PDO("your_database_connection_details");

            // Fetch main categories (where parent_id is 0)
            $select_main_categories = $conn->prepare("SELECT * FROM categories WHERE parent_id = 0");
            $select_main_categories->execute();
            $main_categories = $select_main_categories->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <li><a href="#">Categories<i class="fa-solid fa-chevron-down"></i></a>
                <div class="subnav">
                    <ul>
                        <?php foreach ($main_categories as $main_category) : ?>
                            <li>
                                <a href="#"><i class="fa-solid fa-chevron-right"></i> <?php echo $main_category['name']; ?></a>
                                <?php
                                // Fetch subcategories for the current main category
                                $select_subcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = ?");
                                $select_subcategories->execute([$main_category['id']]);
                                $subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if (!empty($subcategories)) : ?>
                                    <div class="subsubnav">
                                        <ul>
                                            <?php foreach ($subcategories as $subcategory) : ?>
                                                <li>
                                                    <a href="#"><i class="fa-solid fa-chevron-right"></i> <?php echo $subcategory['name']; ?></a>
                                                    <?php
                                                    // Fetch subsubcategories for the current subcategory
                                                    $select_subsubcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = ?");
                                                    $select_subsubcategories->execute([$subcategory['id']]);
                                                    $subsubcategories = $select_subsubcategories->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <?php if (!empty($subsubcategories)) : ?>
                                                        <div class="subsubsubnav">
                                                            <ul>
                                                                <?php foreach ($subsubcategories as $subsubcategory) : ?>
                                                                    <li>
                                                                        <a href="#"><i class="fa-solid fa-chevron-right"></i> <?php echo $subsubcategory['name']; ?></a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </li>

            <li><a href="service.php">Service</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

</header>