<?php

// Fetch min_price and max_price
$price_query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products";
$price_result = $conn->query($price_query);
$price_data = $price_result->fetch(PDO::FETCH_ASSOC);
$default_min_price = $price_data['min_price'];
$default_max_price = $price_data['max_price'];

$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $default_min_price;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $default_max_price;

// Fetch the total number of products from the database
$select_total_products = $conn->prepare("SELECT COUNT(*) as total_products FROM products WHERE price BETWEEN :min_price AND :max_price");
$select_total_products->bindValue(':min_price', $min_price, PDO::PARAM_INT);
$select_total_products->bindValue(':max_price', $max_price, PDO::PARAM_INT);
$select_total_products->execute();
$total_products = $select_total_products->fetch(PDO::FETCH_ASSOC)['total_products'];

$default_products_per_page = 16;
$default_order_by = "latest";

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

$products_per_page = $_SESSION['products_per_page'];
$display_perpage = isset($perpage_mapping[$products_per_page]) ? $perpage_mapping[$products_per_page] : $products_per_page;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_range = ($current_page - 1) * $products_per_page + 1;
$end_range = min($current_page * $products_per_page, $total_products);

$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $default_order_by;
$_SESSION['orderby'] = $order_by;

$order_clauses = [
    'latest' => "id DESC",
    'priceLowToHigh' => "price ASC",
    'priceHighToLow' => "price DESC",
    'a-z' => "name ASC",
    'z-a' => "name DESC"
];

$order_by_clause = isset($order_clauses[$order_by]) ? $order_clauses[$order_by] : $order_clauses[$default_order_by];
$availability_clause = "FIELD(availability, 'in stock', 'out of stock') ASC";

$select_products_query = "
    SELECT * FROM products 
    WHERE price BETWEEN :min_price AND :max_price
    ORDER BY $availability_clause, $order_by_clause
    LIMIT :start, :per_page
";

$select_products = $conn->prepare($select_products_query);
$select_products->bindValue(':min_price', $min_price, PDO::PARAM_INT);
$select_products->bindValue(':max_price', $max_price, PDO::PARAM_INT);
$select_products->bindValue(':start', ($current_page - 1) * $products_per_page, PDO::PARAM_INT);
$select_products->bindValue(':per_page', $products_per_page, PDO::PARAM_INT);
$select_products->execute();
$products = $select_products->fetchAll(PDO::FETCH_ASSOC);

$orderby_mapping = [
    'latest' => 'latest',
    'priceLowToHigh' => 'price (low to high)',
    'priceHighToLow' => 'price (high to low)',
    'a-z' => 'a-z',
    'z-a' => 'z-a',
];

$orderby = $_SESSION['orderby'];
$display_orderby = isset($orderby_mapping[$orderby]) ? $orderby_mapping[$orderby] : $orderby;
