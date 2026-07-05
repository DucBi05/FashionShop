<?php

include("../../config/config.php");

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = '$id'";
mysqli_query($conn, $sql);
$_SESSION['success_message'] = "Xóa người dùng thành công!";
header("Location: index.php");
exit;
