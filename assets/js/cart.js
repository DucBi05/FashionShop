// ============================
// Cart JavaScript — FashionShop
// ============================

/**
 * Thêm sản phẩm vào giỏ hàng qua AJAX
 */
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(getBaseUrl() + 'ajax/add_cart_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật badge giỏ hàng
            updateCartBadge(data.cart_count);
            // Hiện thông báo
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showToast('Không thể thêm vào giỏ hàng!', 'error');
    });
}

/**
 * Cập nhật số trên badge giỏ hàng
 */
function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = count;
        // Animation nhỏ khi cập nhật
        badge.style.transform = 'scale(1.4)';
        setTimeout(() => {
            badge.style.transform = 'scale(1)';
        }, 200);
    }
}

/**
 * Hiển thị toast thông báo
 */
function showToast(message, type = 'success') {
    // Xóa toast cũ nếu có
    const oldToast = document.querySelector('.toast');
    if (oldToast) oldToast.remove();

    // Tạo toast mới
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `${type === 'success' ? '✅' : '❌'} ${message}`;
    document.body.appendChild(toast);

    // Hiện toast
    setTimeout(() => toast.classList.add('show'), 10);

    // Ẩn sau 3 giây
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Lấy base URL của website
 */
function getBaseUrl() {
    // Tìm base URL từ thẻ nav-links hoặc dùng mặc định
    const homeLink = document.querySelector('.nav-links a[href*="index.php"]');
    if (homeLink) {
        return homeLink.href.replace('index.php', '');
    }
    return '/fashion-shop/';
}
