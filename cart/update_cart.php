<?php
// ============================
// Cập nhật số lượng sản phẩm trong giỏ
// ============================
session_start();

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    if ($quantity <= 0) {
        // Xóa nếu số lượng = 0
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Redirect về checkout
header("Location: checkout.php");
exit;
?>
