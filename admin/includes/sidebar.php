<?php

$admin_url = "http://localhost/fashion-shop/admin/";

$current_page = $_SERVER['PHP_SELF'];

?>

<div class="sidebar">

    <div class="logo">
        Fashion Shop Admin
    </div>

    <ul class="sidebar-menu">

        <li>

            <a href="<?= $admin_url ?>dashboard.php" class="<?= strpos($current_page, 'dashboard.php') ? 'active' : '' ?>">

                📊 <span>Dashboard</span>

            </a>

        </li>

        <li>

            <a href="<?= $admin_url ?>products/index.php" class="<?= strpos($current_page, 'products/index.php') ? 'active' : '' ?>">

                📦 <span>Products</span>

            </a>

        </li>

        <li>

            <a href="<?= $admin_url ?>categories/index.php" class="<?= strpos($current_page, 'categories/index.php') ? 'active' : '' ?>">

                🏷️ <span>Categories</span>

            </a>

        </li>

        <li>

            <a href="<?= $admin_url ?>orders/index.php" class="<?= strpos($current_page, 'orders/index.php') ? 'active' : '' ?>">

                🛒 <span>Orders</span>

            </a>

        </li>

        <li>

            <a href="<?= $admin_url ?>users/index.php" class="<?= strpos($current_page, 'users/index.php') ? 'active' : '' ?>">

                👥 <span>Users</span>

            </a>

        </li>

        <li>

            <a href="<?= $admin_url ?>logout.php">

                🚪 <span>Logout</span>

            </a>

        </li>

    </ul>

</div>