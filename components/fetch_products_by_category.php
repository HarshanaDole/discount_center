<?php

// Function to fetch products for a given category ID and its subcategories
function fetchProductsByCategory($conn, $categoryID, $min_price, $max_price, $orderby, $start, $products_per_page)
{
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = :category_id AND price BETWEEN :min_price AND :max_price ORDER BY FIELD(availability, 'in stock', 'out of stock') ASC, $orderby LIMIT :start, :per_page");
    $select_products->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
    $select_products->bindValue(':min_price', $min_price, PDO::PARAM_INT);
    $select_products->bindValue(':max_price', $max_price, PDO::PARAM_INT);
    $select_products->bindValue(':start', $start, PDO::PARAM_INT);
    $select_products->bindValue(':per_page', $products_per_page, PDO::PARAM_INT);
    $select_products->execute();
    $products = $select_products->fetchAll(PDO::FETCH_ASSOC);

    return $products;
}

// Function to recursively fetch all subcategories
function fetchSubcategories($conn, $categoryID)
{
    $subcategories = [];

    $select_subcategories = $conn->prepare("SELECT * FROM categories WHERE parent_id = :category_id");
    $select_subcategories->bindParam(':category_id', $categoryID);
    $select_subcategories->execute();
    $subcategories = $select_subcategories->fetchAll(PDO::FETCH_ASSOC);

    foreach ($subcategories as &$subcategory) {
        // Recursive call to get subsubcategories
        $subcategory['subcategories'] = fetchSubcategories($conn, $subcategory['id']);
    }

    return $subcategories;
}

// Assuming you have a database connection $conn established

// Get the category name from the URL parameter
$categoryName = urldecode($_GET['category']);

// Fetch the category ID based on the category name
$select_category_id = $conn->prepare("SELECT id FROM categories WHERE name = :category_name");
$select_category_id->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
$select_category_id->execute();
$categoryID = $select_category_id->fetch(PDO::FETCH_ASSOC)['id'];


// Fetch min_price and max_price for the selected category
$price_query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE category = :category_id";
$price_result = $conn->prepare($price_query);
$price_result->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
$price_result->execute();
$price_data = $price_result->fetch(PDO::FETCH_ASSOC);

// Set default values
$default_min_price = $price_data['min_price'];
$default_max_price = $price_data['max_price'];
$default_products_per_page = 16;
$default_order_by = "id DESC";

$products_per_page = $_SESSION['products_per_page'];
$display_perpage = isset($perpage_mapping[$products_per_page]) ? $perpage_mapping[$products_per_page] : $products_per_page;

if (isset($_GET['orderby'])) {
    $_SESSION['orderby'] = $_GET['orderby'];
} else {
    $_SESSION['orderby'] = $default_order_by;
}

// Set user-selected values or use defaults
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $default_min_price;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $default_max_price;
$products_per_page = isset($_GET['products_per_page']) ? (int)$_GET['products_per_page'] : $default_products_per_page;
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : $default_order_by;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_range = ($current_page - 1) * $products_per_page;


// Fetch products for the main category and its subcategories
$products = fetchProductsByCategory($conn, $categoryID, $min_price, $max_price, $orderby, $start_range, $products_per_page);

// Fetch subcategories
$subcategories = fetchSubcategories($conn, $categoryID);

$total_products = count($products);

// Calculate total products count for subcategories and subsubcategories
foreach ($subcategories as $subcategory) {
    $subProducts = fetchProductsByCategory($conn, $subcategory['id'], 0, PHP_INT_MAX, 'id DESC', 0, PHP_INT_MAX);
    $total_products += count($subProducts);

    foreach ($subcategory['subcategories'] as $subsubcategory) {
        $subsubProducts = fetchProductsByCategory($conn, $subsubcategory['id'], 0, PHP_INT_MAX, 'id DESC', 0, PHP_INT_MAX);
        $total_products += count($subsubProducts);
    }
}

$end_range = min($current_page * $products_per_page, $total_products);

// Fetch products for each subcategory and its subcategories
foreach ($subcategories as $subcategory) {
    $subProducts = fetchProductsByCategory($conn, $subcategory['id'], 0, PHP_INT_MAX, 'id DESC', 0, PHP_INT_MAX);
    $products = array_merge($products, $subProducts);

    // If there are subsubcategories, fetch their products as well
    foreach ($subcategory['subcategories'] as $subsubcategory) {
        $subsubProducts = fetchProductsByCategory($conn, $subsubcategory['id'], 0, PHP_INT_MAX, 'id DESC', 0, PHP_INT_MAX);
        $products = array_merge($products, $subsubProducts);
    }
}


if (isset($_GET['products_per_page'])) {
    if ($_GET['products_per_page'] == 'All') {
        $_GET['products_per_page'] = $total_products;
    }
    $_SESSION['products_per_page'] = $_GET['products_per_page'];
} else {
    $_SESSION['products_per_page'] = $default_products_per_page;
}

$perpage_mapping = [
    '16' => '16',
    '28' => '28',
    '40' => '40',
    $total_products => 'all',
];

$orderby_mapping = [
    'latest' => 'latest',
    'priceLowToHigh' => 'price (low to high)',
    'priceHighToLow' => 'price (high to low)',
    'a-z' => 'a-z',
    'z-a' => 'z-a',
];

$orderby = $_SESSION['orderby'];
$display_orderby = isset($orderby_mapping[$orderby]) ? $orderby_mapping[$orderby] : $orderby;



?>
