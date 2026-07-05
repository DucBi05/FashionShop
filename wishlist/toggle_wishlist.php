<?php

session_start();

include('../config/config.php');

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']))
{
    echo json_encode([
        'status' => 'login_required'
    ]);
    exit;
}

$user_id    = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

$check = mysqli_query(
    $conn,
    "
    SELECT id
    FROM wishlist
    WHERE user_id = $user_id
    AND product_id = $product_id
    "
);

if(mysqli_num_rows($check) > 0)
{
    mysqli_query(
        $conn,
        "
        DELETE FROM wishlist
        WHERE user_id = $user_id
        AND product_id = $product_id
        "
    );

    echo json_encode([
        'status' => 'removed'
    ]);
}
else
{
    mysqli_query(
        $conn,
        "
        INSERT INTO wishlist
        (
            user_id,
            product_id
        )
        VALUES
        (
            $user_id,
            $product_id
        )
        "
    );

    echo json_encode([
        'status' => 'added'
    ]);
}