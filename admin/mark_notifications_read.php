<?php

include("../config/config.php");

mysqli_query(
    $conn,
    "UPDATE notifications
     SET is_read = 1
     WHERE is_read = 0"
);

header("Location: dashboard.php");
exit;