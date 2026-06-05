<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity   = (int)($_POST['quantity']   ?? 1);
$session_id = session_id();

$result  = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

$existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE product_id = $product_id AND session_id = '$session_id'"));

if ($existing) {
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + $quantity WHERE product_id = $product_id AND session_id = '$session_id'");
} else {
    mysqli_query($conn, "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$session_id', $product_id, $quantity)");
}

$cart_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as t FROM cart WHERE session_id = '$session_id'"))['t'] ?? 0;

echo json_encode([
    'success'    => true,
    'message'    => 'Đã thêm "' . $product['name'] . '" vào giỏ hàng!',
    'cart_count' => $cart_count,
]);