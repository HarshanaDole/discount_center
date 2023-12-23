<?php
include 'connect.php';

$newOrderCount = 0;

$select_new_orders = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE payment_status = ? AND seen = ?");
$select_new_orders->execute(['processing', 0]);
$newOrderCount = $select_new_orders->fetchColumn();

echo json_encode(['newOrderCount' => $newOrderCount]);
?>
