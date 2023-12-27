<footer class="footer">

    <!-- <section class="flex"> -->

    <section class="column-container">

        <ul class="column">

            <div class="footer-logo"><img src="img/dclogocrop.jpg" alt="DC Logo"></div>

        </ul>

        <?php
        // Assuming you have a function to fetch the latest products from the database
        function getLatestProducts($conn, $limit = 5)
        {
            $select_latest_products = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit");
            $select_latest_products->bindParam(':limit', $limit, PDO::PARAM_INT);
            $select_latest_products->execute();

            return $select_latest_products->fetchAll(PDO::FETCH_ASSOC);
        }

        // Fetch the latest 5 products
        $latest_products = getLatestProducts($conn, 5);
        ?>

        <ul class="column">
            <h2 class="column-title">latest products</h2>

            <?php foreach ($latest_products as $product) : ?>
                <li class="product-widget">
                    <a class="img-a" href="product_view.php?pid=<?php echo $product['id']; ?>">
                        <div class="img-container">
                            <img src="uploaded_img/<?php echo $product['image_01']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                    </a>
                    <div class="details">
                        <a class="name-a" href="product_view.php?pid=<?php echo $product['id']; ?>">
                            <span title="<?php echo $product['name']; ?>" class="product-name"><?php echo $product['name']; ?></span>
                        </a>
                        <div class="prices">
                            <span class="newprice">Rs.<?php echo number_format($product['price']); ?></span>
                            <span class="oldprice">Rs.<?php echo number_format($product['old_price']); ?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>


        <?php
        $select_featured_products = $conn->prepare("SELECT * FROM products WHERE featured = 1 ORDER BY id LIMIT 3");
        $select_featured_products->execute();
        $featured_products = $select_featured_products->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <ul class="column">
            <h2 class="column-title">top products</h2>

            <?php foreach ($featured_products as $product) : ?>
                <li class="product-widget">
                    <a class="img-a" href="product_view.php?pid=<?php echo $product['id']; ?>">
                        <div class="img-container">
                            <img src="uploaded_img/<?php echo $product['image_01']; ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                    </a>
                    <div class="details">
                        <a class="name-a" href="product_view.php?pid=<?php echo $product['id']; ?>">
                            <span title="<?php echo $product['name']; ?>" class="product-name"><?php echo $product['name']; ?></span>
                        </a>
                        <div class="prices">
                            <span class="newprice">Rs.<?php echo $product['price']; ?></span>
                            <span class="oldprice">Rs.<?php echo $product['old_price']; ?></span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>



        <div class="column">

            <h2 class="column-title">newsletter</h2>

            <div class="input-field">
                <input class="email" type="email" name="email" id="email" value="Your email address">
                <input class="btn" type="submit" value="SIGN UP">
            </div>

        </div>

    </section>

    <!-- </section> -->

</footer>