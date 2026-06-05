<?php
session_start();
include("../includes/navbar.php");

// Lấy order_id từ URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header("Location: ../index.php");
    exit;
}

// Query thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: ../index.php");
    exit;
}

// Query chi tiết sản phẩm trong đơn
$sql_items = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $sql_items);

// Map phương thức thanh toán
$payment_labels = [
    'cod'     => '🚚 Thanh toán khi nhận hàng (COD)',
    'bank'    => '🏦 Chuyển khoản ngân hàng',
    'ewallet' => '📱 Ví điện tử'
];
$payment_label = $payment_labels[$order['payment_method']] ?? $order['payment_method'];

// Map trạng thái
$status_labels = [
    'pending'   => ['⏳ Chờ xác nhận', 'status-pending'],
    'confirmed' => ['✅ Đã xác nhận', 'status-active'],
    'shipping'  => ['🚚 Đang giao hàng', 'status-pending'],
    'delivered' => ['📦 Đã giao hàng', 'status-active'],
    'cancelled' => ['❌ Đã hủy', 'status-sold']
];
$status_info = $status_labels[$order['status']] ?? ['Không rõ', 'status-pending'];
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div style="max-width:700px;margin:2rem auto;padding:0 1rem">

    <!-- Thông báo thành công -->
    <div style="text-align:center;padding:2rem;background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);margin-bottom:1.5rem">

        <div style="font-size:4rem;margin-bottom:0.5rem">✅</div>

        <h1 style="font-size:1.5rem;font-weight:800;color:#198754;margin-bottom:0.5rem">
            Đặt hàng thành công!
        </h1>

        <p style="color:var(--muted);font-size:0.9rem">
            Cảm ơn bạn đã mua hàng tại <strong>StyleVibe</strong>
        </p>

        <div style="display:inline-flex;gap:0.5rem;align-items:center;background:var(--gray);padding:0.6rem 1.25rem;border-radius:50px;margin-top:1rem;font-weight:700">
            Mã đơn hàng:
            <span style="color:var(--accent);font-size:1.1rem">#DH<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
        </div>
    </div>

    <!-- Thông tin khách hàng -->
    <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem;margin-bottom:1rem">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border)">
            👤 Thông tin giao hàng
        </h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;font-size:0.9rem">
            <div>
                <span style="color:var(--muted);font-size:0.8rem">Họ và tên</span><br>
                <strong><?= htmlspecialchars($order['customer_name']) ?></strong>
            </div>
            <div>
                <span style="color:var(--muted);font-size:0.8rem">Số điện thoại</span><br>
                <strong><?= htmlspecialchars($order['customer_phone']) ?></strong>
            </div>
            <div style="grid-column:span 2">
                <span style="color:var(--muted);font-size:0.8rem">Địa chỉ</span><br>
                <strong>
                    <?= htmlspecialchars($order['shipping_address']) ?>
                    <?php if ($order['district']): ?>, <?= htmlspecialchars($order['district']) ?><?php endif; ?>
                    <?php if ($order['city']): ?>, <?= htmlspecialchars($order['city']) ?><?php endif; ?>
                </strong>
            </div>
            <div>
                <span style="color:var(--muted);font-size:0.8rem">Thanh toán</span><br>
                <strong><?= $payment_label ?></strong>
            </div>
            <div>
                <span style="color:var(--muted);font-size:0.8rem">Trạng thái</span><br>
                <span class="status-badge <?= $status_info[1] ?>"><?= $status_info[0] ?></span>
            </div>
        </div>

        <?php if (!empty($order['note'])): ?>
            <div style="margin-top:0.75rem;font-size:0.85rem">
                <span style="color:var(--muted)">📝 Ghi chú:</span>
                <?= htmlspecialchars($order['note']) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem;margin-bottom:1rem">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border)">
            📦 Chi tiết đơn hàng
        </h3>

        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <div style="display:flex;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid var(--border);align-items:center">

                <div style="width:50px;height:50px;border-radius:8px;overflow:hidden;flex-shrink:0;background:var(--gray)">
                    <img
                        src="../assets/uploads/products/<?= $item['product_image'] ?>"
                        alt="<?= htmlspecialchars($item['product_name']) ?>"
                        style="width:100%;height:100%;object-fit:cover">
                </div>

                <div style="flex:1;min-width:0">
                    <div style="font-weight:600;font-size:0.85rem">
                        <?= htmlspecialchars($item['product_name']) ?>
                    </div>
                    <div style="font-size:0.8rem;color:var(--muted)">
                        <?= number_format($item['price']) ?>đ × <?= $item['quantity'] ?>
                    </div>
                </div>

                <div style="font-weight:700;color:var(--accent);font-size:0.9rem;white-space:nowrap">
                    <?= number_format($item['price'] * $item['quantity']) ?>đ
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Tổng kết tiền -->
        <div style="margin-top:1rem">
            <div class="summary-row">
                <span>Tạm tính</span>
                <span><?= number_format($order['subtotal']) ?>đ</span>
            </div>
            <div class="summary-row">
                <span>Phí vận chuyển</span>
                <span>
                    <?php if ($order['shipping_fee'] == 0): ?>
                        <span style="color:#198754;font-weight:600">Miễn phí</span>
                    <?php else: ?>
                        <?= number_format($order['shipping_fee']) ?>đ
                    <?php endif; ?>
                </span>
            </div>
            <div class="summary-row" style="font-size:1.15rem;font-weight:700;color:var(--accent);border-bottom:none">
                <span>Tổng thanh toán</span>
                <span><?= number_format($order['total']) ?>đ</span>
            </div>
        </div>
    </div>

    <!-- Nút hành động -->
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:1.5rem">
        <a href="../index.php" class="btn btn-primary">
            🏠 Về trang chủ
        </a>
        <a href="../products/shop.php" class="btn btn-outline" style="color:var(--text);border-color:var(--border)">
            🛍️ Tiếp tục mua sắm
        </a>
    </div>

    <!-- Thời gian đặt -->
    <p style="text-align:center;margin-top:1.5rem;font-size:0.8rem;color:var(--muted)">
        🕐 Đặt lúc: <?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?>
    </p>

</div>

<?php
include("../includes/footer.php");
?>
