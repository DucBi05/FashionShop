<?php

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

$timeout = 1800;

if (isset($_SESSION['last_activity'])) {

    if (time() - $_SESSION['last_activity'] > $timeout) {

        session_unset();
        session_destroy();

        header("Location: http://localhost/fashion-shop/admin/login.php");
        exit;
    }
}

$_SESSION['last_activity'] = time();

if(!isset($_SESSION['admin_id']))
{
    header("Location: http://localhost/fashion-shop/admin/login.php");
    exit;
}