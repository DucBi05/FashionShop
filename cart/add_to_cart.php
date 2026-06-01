<?php
// ============================
// Thêm sản phẩm vào giỏ hàng (qua form POST)
// ============================
session_start();
include("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../products/shop.php");
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    header("Location: ../products/shop.php");
    exit;
}

// Kiểm tra sản phẩm tồn tại
$sql = "SELECT id FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: ../products/shop.php");
    exit;
}

// Khởi tạo giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Thêm hoặc cập nhật số lượng
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Redirect về trang trước đó hoặc giỏ hàng
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../cart/cart.php';
header("Location: $redirect");
exit;
?>
