<?php
session_start();
include("../includes/navbar.php");

// Lấy giỏ hàng từ session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_items = [];
$subtotal = 0;

// Query thông tin sản phẩm trong giỏ
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = mysqli_query($conn, $sql);

    while ($product = mysqli_fetch_assoc($result)) {
        $qty = $cart[$product['id']];
        $product['quantity'] = $qty;
        $product['line_total'] = $product['price'] * $qty;
        $subtotal += $product['line_total'];
        $cart_items[] = $product;
    }
}

// Tính phí ship
$shipping_fee = ($subtotal >= 500000 || $subtotal == 0) ? 0 : 30000;
$total = $subtotal + $shipping_fee;
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="checkout-grid">

    <!-- CỘT TRÁI: FORM THÔNG TIN -->
    <div class="checkout-form">
        <h2>📋 Thông tin giao hàng</h2>

        <form id="checkoutForm" action="process_checkout.php" method="POST" onsubmit="return validateCheckout()">

            <!-- Họ tên -->
            <div class="form-group">
                <label>Họ và tên <span style="color:var(--accent)">*</span></label>
                <input
                    type="text"
                    name="customer_name"
                    id="customerName"
                    placeholder="Nguyễn Văn A"
                    required>
            </div>

            <!-- Số điện thoại -->
            <div class="form-group">
                <label>Số điện thoại <span style="color:var(--accent)">*</span></label>
                <input
                    type="tel"
                    name="customer_phone"
                    id="customerPhone"
                    placeholder="0912 345 678"
                    required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email (tuỳ chọn)</label>
                <input
                    type="email"
                    name="customer_email"
                    id="customerEmail"
                    placeholder="email@example.com">
            </div>

            <!-- Tỉnh / Quận -->
            <div class="form-row">
                <div class="form-group">
                    <label>Tỉnh / Thành phố</label>
                    <input
                        type="text"
                        name="city"
                        placeholder="TP. Hồ Chí Minh">
                </div>
                <div class="form-group">
                    <label>Quận / Huyện</label>
                    <input
                        type="text"
                        name="district"
                        placeholder="Quận 1">
                </div>
            </div>

            <!-- Địa chỉ chi tiết -->
            <div class="form-group">
                <label>Địa chỉ giao hàng <span style="color:var(--accent)">*</span></label>
                <textarea
                    name="shipping_address"
                    id="shippingAddress"
                    rows="3"
                    placeholder="Số nhà, tên đường, phường/xã..."
                    required></textarea>
            </div>

            <!-- Ghi chú -->
            <div class="form-group">
                <label>Ghi chú đơn hàng</label>
                <textarea
                    name="note"
                    rows="2"
                    placeholder="Giao giờ hành chính, gọi trước khi giao..."></textarea>
            </div>

            <!-- Phương thức thanh toán -->
            <h2 style="margin-top:1.5rem">💳 Phương thức thanh toán</h2>

            <div class="payment-methods">
                <label class="pay-opt active" onclick="selectPayment(this, 'cod')">
                    <input type="radio" name="payment_method" value="cod" checked style="display:none">
                    🚚 Thanh toán khi nhận hàng (COD)
                </label>
                <label class="pay-opt" onclick="selectPayment(this, 'bank')">
                    <input type="radio" name="payment_method" value="bank" style="display:none">
                    🏦 Chuyển khoản ngân hàng
                </label>
                <label class="pay-opt" onclick="selectPayment(this, 'ewallet')">
                    <input type="radio" name="payment_method" value="ewallet" style="display:none">
                    📱 Ví điện tử (MoMo, ZaloPay)
                </label>
            </div>

            <!-- Nút đặt hàng -->
            <button
                type="submit"
                class="btn btn-primary"
                style="width:100%;justify-content:center;padding:1rem;font-size:1rem;margin-top:1rem"
                <?= empty($cart_items) ? 'disabled' : '' ?>>

                🛒 ĐẶT HÀNG NGAY
                (<?= number_format($total) ?>đ)

            </button>

        </form>
    </div>

    <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG -->
    <div class="order-summary-box">
        <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;padding-bottom:0.75rem;border-bottom:1px solid var(--border)">
            📦 Tóm tắt đơn hàng
        </h2>

        <?php if (empty($cart_items)): ?>

            <div class="empty-cart">
                <div class="icon">🛒</div>
                <p>Giỏ hàng trống</p>
                <a href="../products/shop.php" class="btn btn-primary" style="margin-top:1rem;display:inline-flex">
                    Đi mua sắm
                </a>
            </div>

        <?php else: ?>

            <!-- Danh sách sản phẩm -->
            <?php foreach ($cart_items as $item): ?>
                <div style="display:flex;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid var(--border);align-items:center">

                    <div style="width:55px;height:55px;border-radius:8px;overflow:hidden;flex-shrink:0;background:var(--gray)">
                        <img
                            src="../assets/uploads/products/<?= $item['image'] ?>"
                            alt="<?= $item['name'] ?>"
                            style="width:100%;height:100%;object-fit:cover">
                    </div>

                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            <?= htmlspecialchars($item['name']) ?>
                        </div>
                        <div style="font-size:0.8rem;color:var(--muted)">
                            SL: <?= $item['quantity'] ?>
                        </div>
                    </div>

                    <div style="font-weight:700;color:var(--accent);font-size:0.9rem;white-space:nowrap">
                        <?= number_format($item['line_total']) ?>đ
                    </div>

                </div>
            <?php endforeach; ?>

            <!-- Tổng kết -->
            <div class="summary-row" style="margin-top:0.75rem">
                <span>Tạm tính (<?= array_sum($cart) ?> sản phẩm)</span>
                <span><?= number_format($subtotal) ?>đ</span>
            </div>

            <div class="summary-row">
                <span>Phí vận chuyển</span>
                <span>
                    <?php if ($shipping_fee == 0 && $subtotal > 0): ?>
                        <span style="color:#198754;font-weight:600">Miễn phí</span>
                    <?php else: ?>
                        <?= number_format($shipping_fee) ?>đ
                    <?php endif; ?>
                </span>
            </div>

            <div class="summary-row" style="font-size:1.1rem;font-weight:700;color:var(--accent);border-bottom:none">
                <span>Tổng thanh toán</span>
                <span><?= number_format($total) ?>đ</span>
            </div>

            <?php if ($subtotal < 500000 && $subtotal > 0): ?>
                <div style="background:#fff8e1;border-radius:8px;padding:0.6rem 0.75rem;margin-top:0.75rem;font-size:0.8rem;color:#92400e">
                    💡 Mua thêm <strong><?= number_format(500000 - $subtotal) ?>đ</strong> để được miễn phí vận chuyển!
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

</div>

<!-- JS validate -->
<script>
function selectPayment(el, method) {
    document.querySelectorAll('.pay-opt').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked = true;
}

function validateCheckout() {
    const name = document.getElementById('customerName').value.trim();
    const phone = document.getElementById('customerPhone').value.trim();
    const address = document.getElementById('shippingAddress').value.trim();

    if (!name) {
        alert('Vui lòng nhập họ và tên!');
        document.getElementById('customerName').focus();
        return false;
    }

    if (!phone) {
        alert('Vui lòng nhập số điện thoại!');
        document.getElementById('customerPhone').focus();
        return false;
    }

    // Validate SĐT Việt Nam
    const phoneRegex = /^(0[3|5|7|8|9])[0-9]{8}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        alert('Số điện thoại không hợp lệ! (VD: 0912345678)');
        document.getElementById('customerPhone').focus();
        return false;
    }

    if (!address) {
        alert('Vui lòng nhập địa chỉ giao hàng!');
        document.getElementById('shippingAddress').focus();
        return false;
    }

    return true;
}
</script>

<?php
include("../includes/footer.php");
?>
