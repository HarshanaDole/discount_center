<?php

// Function to fetch all products for a given category ID and its subcategories
function fetchProductsByCategory($conn, $categoryID, $min_price, $max_price, $orderby)
{
    // Define a mapping of sorting options to database columns
    $sortingOptions = [
        'latest' => 'id DESC',
        'priceLowToHigh' => 'price ASC',
        'priceHighToLow' => 'price DESC',
        'a-z' => 'name ASC',
        'z-a' => 'name DESC',
    ];

    // Validate and set the actual column for sorting
    $order_by_clause = isset($sortingOptions[$orderby]) ? $sortingOptions[$orderby] : 'id DESC';
    $availability_clause = "FIELD(availability, 'in stock', 'out of stock') ASC";

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = :category_id AND (price BETWEEN :min_price AND :max_price) ORDER BY $availability_clause, $order_by_clause");
    $select_products->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
    $select_products->bindValue(':min_price', $min_price, PDO::PARAM_INT);
    $select_products->bindValue(':max_price', $max_price, PDO::PARAM_INT);
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


// Get the category name from the URL parameter
$categoryName = urldecode($_GET['category']);

// Fetch the category ID based on the category name
$select_category_id = $conn->prepare("SELECT id FROM categories WHERE name = :category_name");
$select_category_id->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
$select_category_id->execute();
$categoryID = $select_category_id->fetch(PDO::FETCH_ASSOC)['id'];


// Fetch all subcategories and sub-subcategories under the selected category
$category_query = "
    SELECT id
    FROM categories
    WHERE id = :category_id OR parent_id = :category_id OR parent_id IN (
        SELECT id FROM categories WHERE parent_id = :category_id
    )
";

try {
    // Fetch the minimum and maximum prices from the products table for the selected categories
    $price_query = "
        SELECT MIN(price) as min_price, MAX(price) as max_price
        FROM products
        WHERE category IN ({$category_query})
    ";
    $price_result = $conn->prepare($price_query);
    $price_result->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
    $price_result->execute();
    $price_data = $price_result->fetch(PDO::FETCH_ASSOC);

    // Set default values if no products are available
    $default_min_price = $price_data['min_price'];
    $default_max_price = $price_data['max_price'];
} catch (PDOException $e) {
    // Handle any database errors
    die("Error: " . $e->getMessage());
}


// Set default values
$default_products_per_page = 16;
$default_order_by = "id DESC";
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : $default_min_price;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : $default_max_price;

// Calculate total products count for all categories
$total_products_query = "
    SELECT COUNT(*) as total_count
    FROM products
    WHERE category IN ({$category_query}) AND price BETWEEN :min_price AND :max_price
";
$total_products_statement = $conn->prepare($total_products_query);
$total_products_statement->bindValue(':min_price', $min_price, PDO::PARAM_INT);
$total_products_statement->bindValue(':max_price', $max_price, PDO::PARAM_INT);
$total_products_statement->bindValue(':category_id', $categoryID, PDO::PARAM_INT);
$total_products_statement->execute();
$total_products_data = $total_products_statement->fetch(PDO::FETCH_ASSOC);
$total_products = $total_products_data['total_count'];

// Set user-selected values or use defaults
$products_per_page = isset($_GET['products_per_page']) ? (int)$_GET['products_per_page'] : $default_products_per_page;
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : $default_order_by;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


// Fetch products for the main category and its subcategories
$products = fetchProductsByCategory($conn, $categoryID, $min_price, $max_price, $orderby);

// Fetch subcategories
$subcategories = fetchSubcategories($conn, $categoryID);

$start = ($current_page - 1) * $products_per_page;
$start_range = $start + 1;
$end_range = min($current_page * $products_per_page, $total_products);

// Fetch products for each subcategory and its subcategories
foreach ($subcategories as $subcategory) {
    // Adjust the start value based on the current page
    $subProducts = fetchProductsByCategory($conn, $subcategory['id'], $min_price, $max_price, $orderby);

    // Add only the necessary number of products to the array
    foreach ($subProducts as $product) {
        $products[] = $product;
    }

    // If there are subsubcategories, fetch their products as well
    foreach ($subcategory['subcategories'] as $subsubcategory) {
        // Adjust the start value based on the current page
        $subsubProducts = fetchProductsByCategory($conn, $subsubcategory['id'], $min_price, $max_price, $orderby);

        // Add only the necessary number of products to the array
        foreach ($subsubProducts as $product) {
            $products[] = $product;
        }
    }
}

// Sort the entire array based on the selected sorting option
usort($products, function ($a, $b) use ($orderby) {
    if ($a['availability'] != $b['availability']) {
        return ($a['availability'] == 'in stock') ? -1 : 1;
    }
    switch ($orderby) {
        case 'latest':
            return $b['id'] - $a['id'];
        case 'priceLowToHigh':
            return $a['price'] - $b['price'];
        case 'priceHighToLow':
            return $b['price'] - $a['price'];
        case 'a-z':
            return strcmp($a['name'], $b['name']);
        case 'z-a':
            return strcmp($b['name'], $a['name']);
        default:
            return $b['id'] - $a['id'];
    }
});

// Extract the products for the current page
$products = array_slice($products, $start, $products_per_page);

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


if (isset($_GET['orderby'])) {
    $_SESSION['orderby'] = $_GET['orderby'];
} else {
    $_SESSION['orderby'] = $default_order_by;
}



$orderby_mapping = [
    'id DESC' => 'latest',
    'latest' => 'latest',
    'priceLowToHigh' => 'price (low to high)',
    'priceHighToLow' => 'price (high to low)',
    'a-z' => 'a-z',
    'z-a' => 'z-a',
];

$orderby = $_SESSION['orderby'];
$display_orderby = isset($orderby_mapping[$orderby]) ? $orderby_mapping[$orderby] : $orderby;
