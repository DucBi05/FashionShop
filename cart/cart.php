<?php
session_start();
include("../config/config.php");
include("../includes/navbar.php");

$session_id = session_id();
$total      = 0;
$discount   = 0;

// Lấy giỏ hàng từ database
$sql = "
    SELECT c.id as cart_id, c.quantity, p.*
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = '$session_id'
";
$result     = mysqli_query($conn, $sql);
$cart_items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $subtotal     = $row['price'] * $row['quantity'];
    $total       += $subtotal;
    $row['subtotal'] = $subtotal;
    $cart_items[] = $row;
}

// Xử lý mã giảm giá
$coupon_msg   = '';
$coupon_codes = ['SALE10' => 10, 'VIP20' => 20, 'FIRST15' => 15];

if (isset($_POST['apply_coupon'])) {
    $code = strtoupper(trim($_POST['coupon_code']));
    if (isset($coupon_codes[$code])) {
        $_SESSION['coupon']      = $code;
        $_SESSION['coupon_rate'] = $coupon_codes[$code];
        $coupon_msg = "success";
    } else {
        unset($_SESSION['coupon']);
        $coupon_msg = "error";
    }
}

if (isset($_SESSION['coupon'])) {
    $discount = round($total * $_SESSION['coupon_rate'] / 100);
}

$ship        = ($total >= 500000) ? 0 : 35000;
$grand_total = $total - $discount + $ship;
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/cart.css">

<div class="cart-wrapper">

    <div class="cart-header">
        <h1>🛒 Giỏ hàng của bạn</h1>
        <a href="../products/shop.php" class="btn-continue">← Tiếp tục mua sắm</a>
    </div>

    <?php if (empty($cart_items)): ?>

        <div class="cart-empty">
            <div class="empty-icon">🛒</div>
            <h2>Giỏ hàng đang trống</h2>
            <p>Hãy thêm sản phẩm để bắt đầu mua sắm!</p>
            <a href="../products/shop.php" class="btn btn-primary">Mua sắm ngay</a>
        </div>

    <?php else: ?>

        <div class="cart-layout">

            <div class="cart-left">

                <div class="cart-table-wrap">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th colspan="2">Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">

                        <?php foreach ($cart_items as $item): ?>
                            <tr id="row-<?= $item['id'] ?>">

                                <td class="td-img">
                                    <img
                                        src="../assets/uploads/products/<?= htmlspecialchars($item['image']) ?>"
                                        alt="<?= htmlspecialchars($item['name']) ?>"
                                        onerror="this.onerror=null;this.src='https://placehold.co/70x70?text=No+Image'">
                                </td>

                                <td class="td-name">
                                    <a href="../products/product_detail.php?id=<?= $item['id'] ?>">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </a>
                                    <small><?= htmlspecialchars($item['description']) ?></small>
                                </td>

                                <td class="td-price">
                                    <?= number_format($item['price']) ?>đ
                                </td>

                                <td class="td-qty">
                                    <div class="qty-control">
                                        <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, -1)">−</button>
                                        <span class="qty-num" id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                                        <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, 1)">+</button>
                                    </div>
                                </td>

                                <td class="td-subtotal" id="sub-<?= $item['id'] ?>">
                                    <?= number_format($item['subtotal']) ?>đ
                                </td>

                                <td class="td-remove">
                                    <button class="btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Xóa">✕</button>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <!-- Mã giảm giá -->
                <div class="coupon-box">
                    <h3>🏷️ Mã khuyến mãi</h3>
                    <p class="coupon-hint">Thử: <strong>SALE10</strong>, <strong>VIP20</strong>, <strong>FIRST15</strong></p>

                    <?php if ($coupon_msg === 'success'): ?>
                        <div class="alert alert-success">✅ Áp dụng mã thành công! Giảm <?= $_SESSION['coupon_rate'] ?>%</div>
                    <?php elseif ($coupon_msg === 'error'): ?>
                        <div class="alert alert-error">❌ Mã không hợp lệ hoặc đã hết hạn</div>
                    <?php endif; ?>

                    <form method="POST" class="coupon-form">
                        <input type="text" name="coupon_code" placeholder="Nhập mã giảm giá..."
                            value="<?= isset($_SESSION['coupon']) ? $_SESSION['coupon'] : '' ?>">
                        <button type="submit" name="apply_coupon" class="btn btn-outline">Áp dụng</button>
                    </form>
                </div>

            </div>

            <!-- RIGHT: Tóm tắt -->
            <div class="cart-right">

                <div class="order-summary">
                    <h3>🧾 Tóm tắt đơn hàng</h3>

                    <div class="summary-rows">
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <span id="summarySubtotal"><?= number_format($total) ?>đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <span id="summaryShip" class="<?= $ship == 0 ? 'free-ship' : '' ?>">
                                <?= $ship == 0 ? 'Miễn phí' : number_format($ship) . 'đ' ?>
                            </span>
                        </div>
                        <?php if ($discount > 0): ?>
                        <div class="summary-row discount-row">
                            <span>Giảm giá (<?= $_SESSION['coupon_rate'] ?>%)</span>
                            <span class="discount-amt">−<?= number_format($discount) ?>đ</span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-row total-row">
                            <span>Tổng cộng</span>
                            <span id="summaryTotal"><?= number_format($grand_total) ?>đ</span>
                        </div>
                    </div>

                    <?php if ($total < 500000): ?>
                    <div class="free-ship-notice">
                        🚚 Mua thêm <strong><?= number_format(500000 - $total) ?>đ</strong> để được miễn phí giao hàng!
                    </div>
                    <?php else: ?>
                    <div class="free-ship-notice success">🎉 Bạn được miễn phí giao hàng!</div>
                    <?php endif; ?>

                    <a href="../orders/checkout.php" class="btn btn-primary btn-checkout">Tiến hành thanh toán →</a>
                    <div class="secure-notice">🔒 Thanh toán được bảo mật SSL</div>
                </div>

                <div class="policy-box">
                    <div class="policy-item"><span>↩️</span><span>Đổi trả 30 ngày</span></div>
                    <div class="policy-item"><span>🛡️</span><span>Bảo hành chính hãng</span></div>
                    <div class="policy-item"><span>🚚</span><span>Giao nhanh 2h nội thành</span></div>
                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/cart.js"></script>

<?php include("../includes/footer.php"); ?>
