function showToast(msg, type = '') {
    let toast = document.getElementById('cartToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'cart-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.className = 'cart-toast' + (type ? ' ' + type : '');
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => toast.classList.remove('show'), 3000);
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

function addToCart(productId, quantity = 1) {
    fetch('/FashionShop/cart/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('🛒 ' + data.message, 'success');
            updateCartBadge(data.cart_count);
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    })
    .catch(() => showToast('❌ Lỗi kết nối!', 'error'));
}

function changeQty(productId, change) {
    const row = document.getElementById('row-' + productId);
    if (row) row.classList.add('loading');

    fetch('/FashionShop/cart/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&change=${change}`
    })
    .then(r => r.json())
    .then(data => {
        if (row) row.classList.remove('loading');
        if (!data.success) { showToast('❌ ' + data.message, 'error'); return; }
        if (data.removed) {
            row?.remove();
            checkEmptyCart();
        } else {
            const qtyEl = document.getElementById('qty-' + productId);
            const subEl = document.getElementById('sub-' + productId);
            if (qtyEl) qtyEl.textContent = data.new_qty;
            if (subEl) subEl.textContent = data.subtotal;
        }
        updateSummary(data);
        updateCartBadge(data.cart_count);
    })
    .catch(() => { if (row) row.classList.remove('loading'); showToast('❌ Lỗi kết nối!', 'error'); });
}

function removeItem(productId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    const row = document.getElementById('row-' + productId);
    if (row) row.classList.add('loading');

    fetch('/FashionShop/cart/remove_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}`
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) { if (row) row.classList.remove('loading'); return; }
        if (row) {
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            setTimeout(() => { row.remove(); checkEmptyCart(); }, 300);
        }
        updateSummary(data);
        updateCartBadge(data.cart_count);
        showToast('🗑️ Đã xóa sản phẩm', 'success');
    })
    .catch(() => { if (row) row.classList.remove('loading'); showToast('❌ Lỗi kết nối!', 'error'); });
}

function updateSummary(data) {
    const subtotalEl = document.getElementById('summarySubtotal');
    const shipEl = document.getElementById('summaryShip');
    const totalEl = document.getElementById('summaryTotal');
    const noticeEl = document.querySelector('.free-ship-notice');
    if (subtotalEl) subtotalEl.textContent = data.total;
    if (shipEl) { shipEl.textContent = data.ship; shipEl.className = data.ship === 'Miễn phí' ? 'free-ship' : ''; }
    if (totalEl) totalEl.textContent = data.grand_total;
    if (noticeEl) {
        if (data.free_ship) { noticeEl.className = 'free-ship-notice success'; noticeEl.textContent = '🎉 Bạn được miễn phí giao hàng!'; }
        else { noticeEl.className = 'free-ship-notice'; noticeEl.innerHTML = `🚚 Mua thêm <strong>${data.remain}</strong> để được miễn phí!`; }
    }
}

function checkEmptyCart() {
    const tbody = document.getElementById('cartBody');
    if (!tbody || tbody.querySelectorAll('tr').length > 0) return;
    location.reload();
}