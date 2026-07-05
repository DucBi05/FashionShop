<?php

include("includes/auth.php");
include("../config/config.php");

/*
|--------------------------------------------------------------------------
| TOTAL PRODUCTS
|--------------------------------------------------------------------------
*/

$product_sql = "
    SELECT COUNT(*) AS total_products
    FROM products
";

$product_result = mysqli_query($conn, $product_sql);

$product_data = mysqli_fetch_assoc($product_result);


/*
|--------------------------------------------------------------------------
| TOTAL ORDERS
|--------------------------------------------------------------------------
*/

$order_sql = "
    SELECT COUNT(*) AS total_orders
    FROM orders
";

$order_result = mysqli_query($conn, $order_sql);

$order_data = mysqli_fetch_assoc($order_result);


/*
|--------------------------------------------------------------------------
| TOTAL REVENUE
|--------------------------------------------------------------------------
*/

$revenue_sql = "
    SELECT SUM(total) AS total_revenue
    FROM orders
    WHERE status = 'delivered'
";

$revenue_result = mysqli_query($conn, $revenue_sql);

$revenue_data = mysqli_fetch_assoc($revenue_result);


/*
|--------------------------------------------------------------------------
| TOTAL USERS
|--------------------------------------------------------------------------
*/

$user_sql = "
    SELECT COUNT(*) AS total_users
    FROM users WHERE role = 'customer'
";

$user_result = mysqli_query($conn, $user_sql);

$user_data = mysqli_fetch_assoc($user_result);

/*
|--------------------------------------------------------------------------
| REVENUE CHART DATA
|--------------------------------------------------------------------------
*/

$chart_sql = "

    SELECT

        MONTH(created_at) AS month,
        SUM(total) AS revenue

    FROM orders

    WHERE status = 'delivered'

    GROUP BY MONTH(created_at)

    ORDER BY MONTH(created_at)

";

$chart_result = mysqli_query($conn, $chart_sql);

$months = [];
$revenues = [];

while($row = mysqli_fetch_assoc($chart_result)) {

    $months[] = 'Month ' . $row['month'];

    $revenues[] = $row['revenue'];
}

/*
|--------------------------------------------------------------------------
| TOP SELLING PRODUCTS
|--------------------------------------------------------------------------
*/

$top_products_sql = "

    SELECT *

    FROM products

    ORDER BY sold DESC

    LIMIT 5

";

$top_products = mysqli_query($conn, $top_products_sql);

/*
|--------------------------------------------------------------------------
| LOW STOCK PRODUCTS
|--------------------------------------------------------------------------
*/

$low_stock_sql = "

    SELECT *

    FROM products

    WHERE stock < 5

    ORDER BY stock ASC

    LIMIT 5

";

$low_stock_products = mysqli_query($conn, $low_stock_sql);

/*
|--------------------------------------------------------------------------
| RECENT ORDERS
|--------------------------------------------------------------------------
*/

$recent_orders_sql = "
    SELECT *
    FROM orders
    ORDER BY id DESC
    LIMIT 5
";

$recent_orders = mysqli_query($conn, $recent_orders_sql);

/*
|--------------------------------------------------------------------------
| ORDER STATUS CHART
|--------------------------------------------------------------------------
*/

$status_sql = "

    SELECT

        status,
        COUNT(*) AS total

    FROM orders

    GROUP BY status

";

$status_result = mysqli_query($conn, $status_sql);

$status_labels = [];
$status_totals = [];

while($row = mysqli_fetch_assoc($status_result)) {

    $status_labels[] = $row['status'];

    $status_totals[] = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Admin CSS -->
    <link
        rel="stylesheet"
        href="assets/css/admin.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <?php include("includes/sidebar.php"); ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <?php include("includes/header.php"); ?>

        <div class="dashboard-content">
            <div class="title mb-4">

                <h2>
                    Dashboard
                </h2>

            </div>

            <!-- STATISTICS -->
            <div class="stats-grid">

                <!-- PRODUCTS -->
                <div class="stat-card">

                    <h3>
                        Total Products
                    </h3>

                    <p>
                        <?= $product_data['total_products'] ?>
                    </p>

                </div>

                <!-- ORDERS -->
                <div class="stat-card">

                    <h3>
                        Total Orders
                    </h3>

                    <p>
                        <?= $order_data['total_orders'] ?>
                    </p>

                </div>

                <!-- REVENUE -->
                <div class="stat-card">

                    <h3>
                        Revenue
                    </h3>

                    <p>
                        <?= number_format($revenue_data['total_revenue'] ?? 0) ?>đ
                    </p>

                </div>

                <!-- USERS -->
                <div class="stat-card">

                    <h3>
                        Customers
                    </h3>

                    <p>
                        <?= $user_data['total_users'] ?>
                    </p>

                </div>

            </div>

            <div class="charts-grid-dashboard-grid">

                <!-- Revenue CHART -->
                <div class="chart-dashboard-container">

                    <h4 class="mb-4">
                        Revenue Overview
                    </h4>

                    <canvas id="revenueChart"></canvas>

                </div>

                <!-- Doughnut Chart -->
                <div class="chart-dashboard-container">

                    <h4 class="mb-4">
                        Order Status
                    </h4>

                    <canvas id="statusChart"></canvas>

                </div>
            </div>

            <div class="charts-grid-dashboard-grid">

                <!-- TOP SELLING PRODUCTS -->
                <div class="chart-dashboard-container">

                    <div class="d-flex justify-content-between mb-3">

                        <h4>
                            Top Selling Products
                        </h4>

                        <a
                            href="products/index.php"
                            class="button-dashboard">

                            📦 View All

                        </a>

                    </div>

                    <table class="table align-middle" style="text-align: center">

                        <thead>

                            <tr>

                                <th>Image</th>
                                <th>Product</th>
                                <th>Sold</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($product = mysqli_fetch_assoc($top_products)) { ?>

                            <tr>

                                <td>

                                    <img
                                        src="../assets/uploads/products/<?= $product['image'] ?>"
                                        class="product-image">

                                </td>

                                <td>

                                    <?= $product['name'] ?>

                                </td>

                                <td>

                                    <span class="sold-badge">

                                        <?= $product['sold'] ?> sold

                                    </span>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

                <!-- LOW STOCK -->
                <div class="chart-dashboard-container">

                    <div class="d-flex justify-content-between mb-3">

                        <h4>
                            Low Stock Warning
                        </h4>

                        <a
                            href="products/index.php"
                            class="button-dashboard">

                            ⚠️ Check Stock

                        </a>

                    </div>

                    <table class="table align-middle" style="text-align: center">

                        <thead>

                            <tr>

                                <th>Product</th>
                                <th>Stock</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while($low = mysqli_fetch_assoc($low_stock_products)) { ?>

                            <tr>

                                <td>

                                    <?= $low['name'] ?>

                                </td>

                                <td>

                                    <span class="stock-warning">

                                        <?= $low['stock'] ?> left

                                    </span>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- RECENT ORDERS -->
            <div class="recent-orders mt-4">

                <div class="d-flex justify-content-between mb-3">

                    <h4>
                        Recent Orders
                    </h4>

                    <a
                        href="orders/index.php"
                        class="button-dashboard">

                        🛒 View All

                    </a>

                </div>

                <table class="table align-middle" style="text-align: center">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($order = mysqli_fetch_assoc($recent_orders)) { ?>

                        <tr>

                            <td>
                                #<?= $order['id'] ?>
                            </td>

                            <td>
                                <?= $order['customer_name'] ?>
                            </td>

                            <td>
                                <?= $order['customer_phone'] ?>
                            </td>

                            <td>
                                <?= number_format($order['total']) ?>đ
                            </td>

                            <td>

                                <span class="status-badge" style="background: <?= $order['status'] == 'Delivered' ? '#f56223' 
                                : ($order['status'] == 'Confirmed' ? '#f33d7c' 
                                : ($order['status'] == 'Shipping' ? '#1bce51' 
                                : ($order['status'] == 'Cancelled' ? '#3c7de6' : '#ecbf0e'))) ?>;">

                                    <?= $order['status'] ?>

                                </span>

                            </td>

                            <td>
                                <?= $order['created_at'] ?>
                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <?php include("includes/footer.php"); ?>

        </div>

    </div>

</div>

<script>

// Revenue Chart
const ctx = document.getElementById('revenueChart');

const revenueChart = new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?= json_encode($months) ?>,

        datasets: [{

            label: 'Revenue',

            data: <?= json_encode($revenues) ?>,

            borderWidth: 1,

            type: 'line'

        }]
    },

    options: {

        responsive: true,

        scales: {

            y: {

                beginAtZero: true

            }

        }

    }

});

// Doughnut Chart
const statusCtx = document.getElementById('statusChart');

const statusChart = new Chart(statusCtx, {

    type: 'doughnut',

    data: {

        labels: <?= json_encode($status_labels) ?>,

        datasets: [{

            data: <?= json_encode($status_totals) ?>

        }]
    },

    options: {

        responsive: true

    }

});

</script>

</body>
</html>