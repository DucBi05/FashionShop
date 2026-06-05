<?php
/**
 * ajax/add_to_cart.php
 * Nhận: product_id, quantity (POST)
 * Trả:  JSON { success, message, cart_count }
 */
session_start();
header('Content-Type: application/json');

include("../config/config.php");

$product_id = (int) ($_POST['product_id'] ?? 0);
$quantity   = (int) ($_POST['quantity']   ?? 1);

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

// Kiểm tra sản phẩm tồn tại và còn hàng
$sql    = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

if ($product['stock'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng']);
    exit;
}

// Khởi tạo giỏ hàng
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Cập nhật hoặc thêm mới
if (isset($_SESSION['cart'][$product_id])) {
    $new_qty = $_SESSION['cart'][$product_id]['quantity'] + $quantity;

    // Không vượt quá tồn kho
    if ($new_qty > $product['stock']) {
        $new_qty = $product['stock'];
    }

    $_SESSION['cart'][$product_id]['quantity'] = $new_qty;
} else {
    $_SESSION['cart'][$product_id] = [
        'quantity' => min($quantity, $product['stock']),
    ];
}

// Tổng số lượng trong giỏ
$cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

echo json_encode([
    'success'    => true,
    'message'    => 'Đã thêm "' . $product['name'] . '" vào giỏ hàng!',
    'cart_count' => $cart_count,
]);
