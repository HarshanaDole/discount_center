<?php
// Assuming you have a database connection
// $conn = new PDO("your_database_connection_details");

// Fetch main categories (where parent_id is 0)
$select_main_categories = $conn->prepare("SELECT * FROM categories WHERE parent_id = 0");
$select_main_categories->execute();
$main_categories = $select_main_categories->fetchAll(PDO::FETCH_ASSOC);

// Loop through main categories and generate HTML
foreach ($main_categories as $main_category) {
    echo '<li><a href="#">' . $main_category['name'] . '</a></li>';
}
