<?php

include("../../config/config.php");

$id = $_GET['id'];

$sql = "DELETE FROM products WHERE id = '$id'";

mysqli_query($conn, $sql);

$_SESSION['success_message'] = "Xóa sản phẩm thành công!";

header("Location: index.php");
exit;