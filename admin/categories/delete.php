<?php

include("../../config/config.php");

$id = $_GET['id'];

$sql = "DELETE FROM categories WHERE id = '$id'";

mysqli_query($conn, $sql);

$_SESSION['success_message'] = "Xóa danh mục thành công!";

header("Location: index.php");
exit;