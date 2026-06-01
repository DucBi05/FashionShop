<?php
// ============================
// Xóa sản phẩm khỏi giỏ hàng
// ============================
session_start();

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

// Redirect về trang checkout
header("Location: checkout.php");
exit;
?>
