<?php
/**
 * ajax/update_cart.php
 * Nhận: product_id, change (+1 hoặc -1)
 * Trả:  JSON { success, new_qty, subtotal, total, ship, grand_total, cart_count }
 */
session_start();
header('Content-Type: application/json');

include("../config/config.php");

$product_id = (int) ($_POST['product_id'] ?? 0);
$change     = (int) ($_POST['change']     ?? 0);

if ($product_id <= 0 || !in_array($change, [-1, 1])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

if (!isset($_SESSION['cart'][$product_id])) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ']);
    exit;
}

// Lấy giá sản phẩm
$sql    = "SELECT price, stock FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

$current_qty = $_SESSION['cart'][$product_id]['quantity'];
$new_qty     = $current_qty + $change;

// Xóa nếu qty về 0
if ($new_qty <= 0) {
    unset($_SESSION['cart'][$product_id]);

    // Tính lại tổng
    list($total, $ship, $grand_total) = calcTotal($conn);

    echo json_encode([
        'success'     => true,
        'removed'     => true,
        'total'       => number_format($total) . 'đ',
        'ship'        => $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ',
        'grand_total' => number_format($grand_total) . 'đ',
        'cart_count'  => array_sum(array_column($_SESSION['cart'], 'quantity')),
    ]);
    exit;
}

// Không vượt tồn kho
if ($new_qty > $product['stock']) {
    echo json_encode(['success' => false, 'message' => 'Không đủ hàng trong kho']);
    exit;
}

$_SESSION['cart'][$product_id]['quantity'] = $new_qty;

$subtotal = $product['price'] * $new_qty;

list($total, $ship, $grand_total) = calcTotal($conn);

echo json_encode([
    'success'     => true,
    'removed'     => false,
    'new_qty'     => $new_qty,
    'subtotal'    => number_format($subtotal) . 'đ',
    'total'       => number_format($total) . 'đ',
    'ship'        => $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ',
    'grand_total' => number_format($grand_total) . 'đ',
    'cart_count'  => array_sum(array_column($_SESSION['cart'], 'quantity')),
    'free_ship'   => $total >= 500000,
    'remain'      => $total < 500000 ? number_format(500000 - $total) . 'đ' : '',
]);

// ── Helper ──────────────────────────────────────────────────────
function calcTotal($conn) {
    $cart  = $_SESSION['cart'];
    $total = 0;

    if (!empty($cart)) {
        $ids = implode(',', array_keys($cart));
        $res = mysqli_query($conn, "SELECT id, price FROM products WHERE id IN ($ids)");

        while ($p = mysqli_fetch_assoc($res)) {
            $total += $p['price'] * $cart[$p['id']]['quantity'];
        }
    }

    // Trừ coupon nếu có
    $discount = 0;
    if (isset($_SESSION['coupon_rate'])) {
        $discount = round($total * $_SESSION['coupon_rate'] / 100);
    }

    $ship        = ($total >= 500000) ? 0 : 35000;
    $grand_total = $total - $discount + $ship;

    return [$total, $ship, $grand_total];
}
