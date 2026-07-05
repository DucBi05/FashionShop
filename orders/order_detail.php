<?php

session_start();
include("../config/config.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/login.php");
    exit;
}

$id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

$sql = "
SELECT *
FROM orders
WHERE id = $id
AND user_id = ".$_SESSION['user_id'];

$order =
    mysqli_fetch_assoc(
        mysqli_query($conn,$sql)
    );

if(!$order)
{
    header("Location: orders.php");
    exit;
}

$items = mysqli_query(
    $conn,
    "
    SELECT *
    FROM order_items oi
    JOIN products p
    ON oi.product_id = p.id
    WHERE oi.order_id = $id
    "
);

include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/orders.css">

<div class="order-detail-header">

    <div>
        <h1>Đơn hàng #ORD<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h1>
        <span><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
    </div>
    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
        <?= strtoupper(htmlspecialchars($order['status'])) ?>
    </span>

</div>

<div class="detail-card">

    <h3>📍 Thông tin nhận hàng</h3>

    <div class="info-row">

        <strong>Người nhận:</strong>

        <span><?= htmlspecialchars($order['customer_name']) ?></span>

    </div>

    <div class="info-row">

        <strong>Số điện thoại:</strong>

        <span><?= htmlspecialchars($order['customer_phone']) ?></span>

    </div>

    <div class="info-row">

        <strong>Địa chỉ:</strong>

        <span><?= htmlspecialchars($order['shipping_address']) ?></span>

    </div>

</div>

<div class="detail-card">

    <h3>🛍 Sản phẩm đã đặt</h3>

    <?php

    $total = 0;

    while($item = mysqli_fetch_assoc($items)):

    $subtotal =
        $item['price']
        * $item['quantity'];

    $total += $subtotal;

    ?>

    <div class="order-product-row">

        <img
            src="../assets/uploads/products/<?= $item['image'] ?>"
            class="product-thumb">

        <div class="product-content">

            <h4><?= htmlspecialchars($item['name']) ?></h4>

            <span>
                Số lượng:
                <?= (int)$item['quantity'] ?>
            </span>

        </div>

        <div class="product-price">

            <?= number_format($item['price']) ?>đ

        </div>

    </div>

    <?php endwhile; ?>

</div>

<div class="detail-card total-card">

    <div>

        <h3>Tổng thanh toán</h3>

        <small>
            Đã bao gồm phí vận chuyển
        </small>

    </div>


    <div class="grand-total">

        <?= number_format($order['total']) ?>đ

    </div>

    <?php if(
        in_array(
            $order['status'],
            ['Pending','Confirmed']
        )
    ): ?>

    <form
        action="cancel_order.php"
        method="POST">

        <input
            type="hidden"
            name="order_id"
            value="<?= (int)$order['id'] ?>">

        <button
            type="submit"
            class="btn-cancel"
            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng?')">

            Hủy đơn hàng

        </button>

    </form>

    <?php endif; ?>

</div>



<div class="detail-card">

    <h3>🚚 Trạng thái đơn hàng</h3>

    <div class="order-timeline">

        <?php if ($order['status'] == 'Cancelled'): ?>
            <div class="step active">

                <div class="circle cancelled"></div>

                <span>Đã hủy</span>

            </div>
        <?php else: ?>
            <div class="step active">

                <div class="circle"></div>

                <span>Đặt hàng</span>

            </div>

            <div class="step <?= in_array(
                $order['status'],
                ['Confirmed','Shipping','Delivered']
            ) ? 'active' : '' ?>">

                <div class="circle"></div>

                <span>Xác nhận</span>

            </div>

            <div class="step <?= in_array(
                $order['status'],
                ['Shipping','Delivered']
            ) ? 'active' : '' ?>">

                <div class="circle"></div>

                <span>Đang giao</span>

            </div>

            <div class="step <?= $order['status']=='Delivered'
                ? 'active'
                : '' ?>">

                <div class="circle"></div>

                <span>Hoàn thành</span>

            </div>
        <?php endif; ?>

    </div>

</div>