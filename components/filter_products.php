<?php
include 'connect.php';

function getMainCategoryName($conn, $category_id)
{
    try {
        // Fetch category information
        $select_category = $conn->prepare("SELECT name, parent_id FROM categories WHERE id = :id");
        $select_category->bindParam(':id', $category_id);
        $select_category->execute();
        $category_info = $select_category->fetch(PDO::FETCH_ASSOC);

        if ($category_info) {
            // Check if the category is already a main category
            if ($category_info['parent_id'] == 0) {
                return $category_info['name'];
            } else {
                // Recursively get the main category name for the parent category
                $main_category_name = getMainCategoryName($conn, $category_info['parent_id']);

                return $main_category_name;
            }
        }

        return ''; // Return an empty string if category information is not found
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return ''; // Return an empty string in case of an error
    }
}

// Get the price range values from the AJAX request
$minPrice = $_POST['minPrice'];
$maxPrice = $_POST['maxPrice'];

// Query to fetch in-stock products within the specified price range
$select_in_stock_products = $conn->prepare("SELECT * FROM products WHERE price BETWEEN :minPrice AND :maxPrice AND availability = 'in stock'");
$select_in_stock_products->bindParam(':minPrice', $minPrice);
$select_in_stock_products->bindParam(':maxPrice', $maxPrice);
$select_in_stock_products->execute();
$in_stock_products = $select_in_stock_products->fetchAll(PDO::FETCH_ASSOC);

// Query to fetch out-of-stock products within the specified price range
$select_out_of_stock_products = $conn->prepare("SELECT * FROM products WHERE price BETWEEN :minPrice AND :maxPrice AND availability = 'out of stock'");
$select_out_of_stock_products->bindParam(':minPrice', $minPrice);
$select_out_of_stock_products->bindParam(':maxPrice', $maxPrice);
$select_out_of_stock_products->execute();
$out_of_stock_products = $select_out_of_stock_products->fetchAll(PDO::FETCH_ASSOC);

// Concatenate in-stock and out-of-stock products
$filtered_products = array_merge($in_stock_products, $out_of_stock_products);

// Output the filtered products
foreach ($filtered_products as $product) {
    echo '<form action="" method="post">';
    echo '<input type="hidden" name="pid" value="' . $product['id'] . '">';
    echo '<input type="hidden" name="name" value="' . $product['name'] . '">';
    echo '<input type="hidden" name="price" value="' . $product['price'] . '">';
    echo '<input type="hidden" name="image" value="' . $product['image_01'] . '">';
    echo '<input type="hidden" name="qty" value="1">';
    echo '<div class="product-card ' . (($product['availability'] === 'out of stock') ? 'out-of-stock' : '') . '">';
    echo '<a class="image-container" href="product_view.php?pid=' . $product['id'] . '">';
    echo '<img src="uploaded_img/' . $product['image_01'] . '" alt="product">';
    echo '</a>';
    echo '<div class="details">';
    echo '<span class="category-name">' . getMainCategoryName($conn, $product['category']) . '</span>';
    echo '<a href="product_view.php?pid=' . $product['id'] . '">';
    echo '<span class="product-name" title="' . $product['name'] . '">' . $product['name'] . '</span>';
    echo '</a>';
    echo '<hr>';
    echo '<div class="loop-btn">';
    echo '<span class="price">Rs. ' . number_format($product['price']) . '</span>';
    if ($product['availability'] === 'out of stock') {
        echo '<button class="btn" type="submit" name="out_of_stock" disabled>Out of Stock</button>';
    } else {
        echo '<button class="option-btn" type="submit" name="add_to_cart">Add to Cart</button>';
    }
    echo '<button class="heart-icon fa-regular fa-heart" type="submit" name="add_to_wishlist"></button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
}
