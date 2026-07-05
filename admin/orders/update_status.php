<?php

include("../../config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    mysqli_query($conn, $sql);
}

$_SESSION['success_message'] = "Cập nhật trạng thái đơn hàng thành công!";

header("Location: index.php");
exit;