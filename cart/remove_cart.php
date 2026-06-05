<?php
/**
 * ajax/remove_cart.php
 * Nhận: product_id (POST)
 * Trả:  JSON { success, total, ship, grand_total, cart_count, cart_empty }
 */
session_start();
header('Content-Type: application/json');

include("../config/config.php");

$product_id = (int) ($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

unset($_SESSION['cart'][$product_id]);

$cart  = $_SESSION['cart'] ?? [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $res = mysqli_query($conn, "SELECT id, price FROM products WHERE id IN ($ids)");

    while ($p = mysqli_fetch_assoc($res)) {
        $total += $p['price'] * $cart[$p['id']]['quantity'];
    }
}

$discount = 0;
if (isset($_SESSION['coupon_rate'])) {
    $discount = round($total * $_SESSION['coupon_rate'] / 100);
}

$ship        = ($total >= 500000) ? 0 : 35000;
$grand_total = $total - $discount + $ship;
$cart_count  = array_sum(array_column($cart, 'quantity'));

echo json_encode([
    'success'     => true,
    'total'       => number_format($total) . 'đ',
    'ship'        => $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ',
    'grand_total' => number_format($grand_total) . 'đ',
    'cart_count'  => $cart_count,
    'cart_empty'  => empty($cart),
    'free_ship'   => $total >= 500000,
    'remain'      => $total < 500000 ? number_format(500000 - $total) . 'đ' : '',
]);
