<?php

session_start();
include("../config/config.php");

if(!isset($_SESSION['user_id']))
{
    exit;
}

$order_id =
    (int)$_POST['order_id'];

$user_id =
    $_SESSION['user_id'];

mysqli_query(
    $conn,
    "
    UPDATE orders
    SET status='Cancelled'
    WHERE id=$order_id
    AND user_id=$user_id
    AND status IN
    (
        'pending',
        'confirmed'
    )
    "
);

header(
    "Location: order_detail.php?id=".$order_id
);