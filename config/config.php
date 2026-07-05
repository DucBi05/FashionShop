<?php

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

$base_url = "http://localhost/fashion-shop/";

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "fashion_shop"
);

mysqli_set_charset($conn, "utf8mb4");

?>