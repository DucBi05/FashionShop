<?php
session_start();
include("../config/config.php");

// ============================
// Chỉ chấp nhận POST
// ============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

// ============================
// Kiểm tra giỏ hàng có sản phẩm không
// ============================
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: checkout.php");
    exit;
}

// ============================
// Nhận dữ liệu từ form
// ============================
$customer_name    = mysqli_real_escape_string($conn, trim($_POST['customer_name'] ?? ''));
$customer_phone   = mysqli_real_escape_string($conn, trim($_POST['customer_phone'] ?? ''));
$customer_email   = mysqli_real_escape_string($conn, trim($_POST['customer_email'] ?? ''));
$shipping_address = mysqli_real_escape_string($conn, trim($_POST['shipping_address'] ?? ''));
$city             = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
$district         = mysqli_real_escape_string($conn, trim($_POST['district'] ?? ''));
$note             = mysqli_real_escape_string($conn, trim($_POST['note'] ?? ''));
$payment_method   = mysqli_real_escape_string($conn, trim($_POST['payment_method'] ?? 'cod'));

// ============================
// Validate bắt buộc
// ============================
$errors = [];

if (empty($customer_name)) {
    $errors[] = "Vui lòng nhập họ và tên";
}

if (empty($customer_phone)) {
    $errors[] = "Vui lòng nhập số điện thoại";
}

if (empty($shipping_address)) {
    $errors[] = "Vui lòng nhập địa chỉ giao hàng";
}

// Validate payment method
$valid_methods = ['cod', 'bank', 'ewallet'];
if (!in_array($payment_method, $valid_methods)) {
    $payment_method = 'cod';
}

// Nếu có lỗi, quay lại checkout
if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    header("Location: checkout.php");
    exit;
}

// ============================
// Tính toán đơn hàng
// ============================
$cart = $_SESSION['cart'];
$cart_items = [];
$subtotal = 0;

// Lấy thông tin sản phẩm từ DB
$ids = implode(',', array_map('intval', array_keys($cart)));
$sql = "SELECT * FROM products WHERE id IN ($ids)";
$result = mysqli_query($conn, $sql);

while ($product = mysqli_fetch_assoc($result)) {
    $qty = (int)$cart[$product['id']];
    $product['quantity'] = $qty;
    $product['line_total'] = $product['price'] * $qty;
    $subtotal += $product['line_total'];
    $cart_items[] = $product;
}

// Phí ship: miễn phí nếu đơn >= 500k
$shipping_fee = ($subtotal >= 500000) ? 0 : 30000;
$total = $subtotal + $shipping_fee;

// ============================
// Lưu vào database — Bảng orders
// ============================
$sql_order = "
    INSERT INTO orders
        (customer_name, customer_phone, customer_email, shipping_address, city, district, note, payment_method, subtotal, shipping_fee, total, status)
    VALUES
        ('$customer_name', '$customer_phone', '$customer_email', '$shipping_address', '$city', '$district', '$note', '$payment_method', $subtotal, $shipping_fee, $total, 'pending')
";

$order_result = mysqli_query($conn, $sql_order);

if (!$order_result) {
    die("Lỗi tạo đơn hàng: " . mysqli_error($conn));
}

// Lấy ID đơn hàng vừa tạo
$order_id = mysqli_insert_id($conn);

// ============================
// Lưu vào database — Bảng order_items
// ============================
foreach ($cart_items as $item) {
    $product_id    = (int)$item['id'];
    $product_name  = mysqli_real_escape_string($conn, $item['name']);
    $product_image = mysqli_real_escape_string($conn, $item['image'] ?? '');
    $price         = (int)$item['price'];
    $quantity      = (int)$item['quantity'];

    $sql_item = "
        INSERT INTO order_items
            (order_id, product_id, product_name, product_image, price, quantity)
        VALUES
            ($order_id, $product_id, '$product_name', '$product_image', $price, $quantity)
    ";

    mysqli_query($conn, $sql_item);
}

// ============================
// Xóa giỏ hàng sau khi đặt thành công
// ============================
unset($_SESSION['cart']);

// ============================
// Chuyển đến trang thành công
// ============================
header("Location: order_success.php?id=" . $order_id);
exit;
?>
