<?php
session_start();
include("../config/config.php");
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']))
{
    echo json_encode([
        'status' => 'login_required'
    ]);
    exit;
}

$product_id = (int)($_POST['product_id']);
$quantity = isset($_POST['quantity'])
    ? (int)$_POST['quantity']
    : 1;

if($quantity < 1){
    $quantity = 1;
}
$session_id = $_SESSION['user_id'];

$product_rs = mysqli_query(
    $conn,
    "
    SELECT stock
    FROM products
    WHERE id = $product_id
    "
);

$product = mysqli_fetch_assoc($product_rs);

if($quantity > $product['stock'])
{
    echo json_encode([
        'status' => 'error',
        'message' => 'Số lượng vượt quá tồn kho'
    ]);
    exit;
}

$check = mysqli_query(
    $conn,
    "
    SELECT *
    FROM cart
    WHERE session_id = $session_id
    AND product_id = $product_id
    "
);

if(mysqli_num_rows($check) > 0)
{
    mysqli_query(
        $conn,
        "
        UPDATE cart
        SET quantity = quantity + $quantity
        WHERE session_id = $session_id
        AND product_id = $product_id
        "
    );
}
else
{
    mysqli_query(
        $conn,
        "
        INSERT INTO cart
        (
            session_id,
            product_id,
            quantity
        )
        VALUES
        (
            $session_id,
            $product_id,
            $quantity
        )
        "
    );
}

$count = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT SUM(quantity) total
        FROM cart
        WHERE session_id = $session_id
        "
    )
);

echo json_encode([
    'status' => 'success',
    'cart_count' => $count['total'] ?? 0
]);

exit;