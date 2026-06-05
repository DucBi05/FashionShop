<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

$product_id = (int)($_POST['product_id'] ?? 0);
$session_id = session_id();

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

mysqli_query($conn, "DELETE FROM cart WHERE product_id = $product_id AND session_id = '$session_id'");

$total = 0;
$res = mysqli_query($conn, "SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = '$session_id'");
while ($row = mysqli_fetch_assoc($res)) {
    $total += $row['price'] * $row['quantity'];
}

$ship        = $total >= 500000 ? 0 : 35000;
$grand_total = $total + $ship;
$cart_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as t FROM cart WHERE session_id = '$session_id'"))['t'] ?? 0;

echo json_encode([
    'success'     => true,
    'total'       => number_format($total) . 'đ',
    'ship'        => $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ',
    'grand_total' => number_format($grand_total) . 'đ',
    'cart_count'  => $cart_count,
    'cart_empty'  => $total == 0,
    'free_ship'   => $total >= 500000,
    'remain'      => $total < 500000 ? number_format(500000 - $total) . 'đ' : '',
]);