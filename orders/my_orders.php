<?php

session_start();
include("../config/config.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit;
}

$status = $_GET['status'] ?? 'all';
$user_id = $_SESSION['user_id'];

$sql = "
    SELECT *
    FROM orders
    WHERE user_id = $user_id
";

if($status != 'all')
{
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY id DESC";

$orders = mysqli_query($conn, $sql);

include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/orders.css">

<div class="orders-container">

    <?php
    $current = $_GET['status'] ?? 'all';
    ?>

    <div class="order-tabs">

        <a
            href="?status=all"
            class="<?= $current=='all'?'active':'' ?>">
            Tất cả
        </a>

        <a
            href="?status=pending"
            class="<?= $current=='pending'?'active':'' ?>">
            Chờ xác nhận
        </a>

        <a
            href="?status=confirmed"
            class="<?= $current=='confirmed'?'active':'' ?>">
            Đã xác nhận
        </a>

        <a
            href="?status=shipping"
            class="<?= $current=='shipping'?'active':'' ?>">
            Đang giao
        </a>

        <a
            href="?status=delivered"
            class="<?= $current=='delivered'?'active':'' ?>">
            Đã giao
        </a>

        <a
            href="?status=cancelled"
            class="<?= $current=='cancelled'?'active':'' ?>">
            Đã hủy
        </a>

    </div>

    <h2 style="margin-bottom: 20px;">Đơn hàng của tôi</h2>

    <?php if (mysqli_num_rows($orders) == 0): ?>
        
        <div class="no-orders">
            <div style="font-size: 64px; color: #ccc; margin-bottom: 15px;">📦</div>
            <p>Bạn chưa có đơn hàng nào trong mục này.</p>
            <a href="../products/shop.php">Tiếp tục mua sắm</a>
        </div>

    <?php else: ?>

        <?php while($order = mysqli_fetch_assoc($orders)): ?>

        <div class="order-card">

            <div class="order-header">

                <div>
                    <strong>
                        #ORD<?= str_pad($order['id'],5,'0',STR_PAD_LEFT) ?>
                    </strong>
                </div>

                <div class="order-status">

                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>

                    <small>
                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </small>

                </div>

            </div>

            <div class="order-body">

                <?php

                $items = mysqli_query(
                    $conn,
                    "
                    SELECT 
                        p.name,
                        p.image,
                        oi.quantity,
                        oi.price
                    FROM order_items oi
                    JOIN products p
                    ON oi.product_id = p.id
                    WHERE oi.order_id = {$order['id']}
                    "
                );

                while($item = mysqli_fetch_assoc($items)):
                ?>

                <div class="order-product">

                    <img
                        src="../assets/uploads/products/<?= $item['image'] ?>"
                        class="order-product-image">

                    <div class="order-product-info">

                        <h4><?= htmlspecialchars($item['name']) ?></h4>

                        <span>
                            Số lượng: <?= $item['quantity'] ?>
                        </span>

                    </div>

                </div>

                <?php endwhile; ?>

            </div>

            <div class="order-footer">

                <div class="order-total">

                    Tổng thanh toán

                    <span>
                        <?= number_format($order['total']) ?>đ
                    </span>

                </div>

                <a
                    href="order_detail.php?id=<?= $order['id'] ?>"
                    class="btn-detail">

                    Xem chi tiết

                </a>

            </div>

        </div>

        <?php endwhile; ?>
        
    <?php endif; ?>

</div>

<?php include("../includes/footer.php"); ?>