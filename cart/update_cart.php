<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

$product_id = (int)($_POST['product_id'] ?? 0);
$change     = (int)($_POST['change']     ?? 0);
$session_id = session_id();

$res     = mysqli_query($conn, "SELECT * FROM cart WHERE product_id = $product_id AND session_id = '$session_id'");
$item    = mysqli_fetch_assoc($res);
$new_qty = $item['quantity'] + $change;

if ($new_qty <= 0) {
    mysqli_query($conn, "DELETE FROM cart WHERE product_id = $product_id AND session_id = '$session_id'");
    $removed = true;
} else {
    mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE product_id = $product_id AND session_id = '$session_id'");
    $removed = false;
}

$total = 0;
$res2 = mysqli_query($conn, "SELECT c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = '$session_id'");
while ($row = mysqli_fetch_assoc($res2)) {
    $total += $row['price'] * $row['quantity'];
}

$p_res   = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
$product = mysqli_fetch_assoc($p_res);
$ship        = $total >= 500000 ? 0 : 35000;
$grand_total = $total + $ship;
$cart_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as t FROM cart WHERE session_id = '$session_id'"))['t'] ?? 0;

echo json_encode([
    'success'     => true,
    'removed'     => $removed,
    'new_qty'     => $new_qty,
    'subtotal'    => number_format($product['price'] * $new_qty) . 'đ',
    'total'       => number_format($total) . 'đ',
    'ship'        => $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ',
    'grand_total' => number_format($grand_total) . 'đ',
    'cart_count'  => $cart_count,
    'free_ship'   => $total >= 500000,
    'remain'      => $total < 500000 ? number_format(500000 - $total) . 'đ' : '',
]);