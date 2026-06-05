<?php
session_start();
include_once(__DIR__ . "/../config/config.php");

header('Content-Type: application/json');

// Nhận dữ liệu
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

// Kiểm tra sản phẩm có tồn tại không
$sql = "SELECT id FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Thêm hoặc cập nhật số lượng
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Đếm tổng số lượng trong giỏ
$cart_count = array_sum($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào giỏ hàng!',
    'cart_count' => $cart_count
]);
?>
