<?php

include("../includes/auth.php");
include("../../config/config.php");

$sql = "
    SELECT *
    FROM orders
";

$keyword = trim($_GET['keyword'] ?? '');

if($keyword != '')
{
    $sql .= "
    WHERE orders.customer_name LIKE '%$keyword%'
    ";
}

$result = mysqli_query($conn, $sql);

$total_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM orders")
)['total'];

$total_pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM orders WHERE status = 'Pending'")
)['total'];

$total_delivered = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM orders WHERE status = 'Delivered'")
)['total'];

?>

<link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">

<div class="admin-layout">

    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">

        <?php include("../includes/header.php"); ?>

        <div class="table-card">

            <?php if(isset($_SESSION['success_message'])) { ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                    <?= $_SESSION['success_message'] ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="this.parentElement.remove();"></button>

                </div>

                <?php unset($_SESSION['success_message']); ?>

            <?php } ?>

            <div class="table-header">

                <h2>Orders</h2>

                <div class="top-bar">

                    <form method="GET" class="search-form">

                        <input
                            type="text"
                            name="keyword"
                            placeholder="Search for orders..."
                            value="<?= $_GET['keyword'] ?? '' ?>">

                        <button type="submit">
                            🔍
                        </button>

                    </form>

                </div>

            </div>

            <div class="product-stats">

                <div class="stat-card">
                    <p>Total orders</p>
                    <h3><?= $total_orders ?></h3>
                </div>

                <div class="stat-card">
                    <p>Pending</p>
                    <h3><?= $total_pending ?></h3>
                </div>

                <div class="stat-card danger">
                    <p>Delivered</p>
                    <h3><?= $total_delivered ?></h3>
                </div>

            </div>

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($order = mysqli_fetch_assoc($result)) { ?>

                        <tr>

                            <td>
                                #<?= $order['id'] ?>
                            </td>

                            <td style="text-align: center;">
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

                            <td>

                                <a
                                    href="detail.php?id=<?= $order['id'] ?>"
                                    class="btn-action btn-edit">

                                    View

                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <?php include("../includes/footer.php"); ?>

    </div>

</div>