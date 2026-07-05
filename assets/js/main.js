/** * MAIN.JS - GỘP CHUNG UI EVENTS (Tối ưu cho PHP Render)
 * Đã loại bỏ các hàm fetch API, phân trang và bộ lọc bằng JS do PHP xử lý.
 */

document.addEventListener('DOMContentLoaded', () => {
    setupUIEvents();
    setupCountdown();
    setupFooter();
});

/* ===================== VIEW MODE ===================== */
// Chuyển đổi giao diện hiển thị Sản phẩm (Dạng lưới / Dạng danh sách)
function setView(mode) {
    const grid = document.getElementById('product-grid');
    const viewGridBtn = document.getElementById('view-grid');
    const viewListBtn = document.getElementById('view-list');

    if (viewGridBtn) viewGridBtn.classList.toggle('active', mode === 'grid');
    if (viewListBtn) viewListBtn.classList.toggle('active', mode === 'list');
    
    if (grid) {
        if (mode === 'list') {
            grid.classList.add('shop-product-list');
        } else {
            grid.classList.remove('shop-product-list');
        }
    }
}

/* ===================== GIỎ HÀNG (DRAWER) ===================== */
function openCart() {
    document.getElementById('cartDrawer')?.classList.add('open');
    document.getElementById('cartOverlay')?.classList.add('open');
    document.body.style.overflow = 'hidden'; // Ngăn cuộn trang nền
}

function closeCart() {
    document.getElementById('cartDrawer')?.classList.remove('open');
    document.getElementById('cartOverlay')?.classList.remove('open');
    document.body.style.overflow = '';
}

/* ===================== UI EVENTS & DELEGATION ===================== */
function setupUIEvents() {
    // Hiệu ứng Header khi cuộn trang
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (header) {
            header.style.boxShadow = (window.scrollY > 60) ? '0 2px 20px rgba(10,10,10,0.08)' : 'none';
        }
    });

    // Slider giá (Nếu bạn còn dùng ở sidebar bộ lọc tĩnh)
    const slider = document.getElementById('price-slider');
    const priceTo = document.getElementById('price-to');
    if (slider && priceTo) {
        slider.addEventListener('input', function() {
            priceTo.value = parseInt(this.value).toLocaleString('vi-VN') + 'đ';
        });
    }

    // Xử lý sự kiện (Event Delegation) cho các nút tương tác động
    document.addEventListener('click', function(e) {
        
        // Nút Quick Add (Nếu có)
        const btnAdd = e.target.closest('.btn-quick-add');
        if (btnAdd) {
            // Ngăn chặn sự kiện mặc định nếu cần (nếu đang nằm trong form thì bỏ dòng dưới)
            // e.preventDefault(); 
            
            const originalText = btnAdd.textContent;
            btnAdd.textContent = '✓ Đã thêm!';
            btnAdd.style.background = '#22A559';
            btnAdd.style.color = '#fff';
            
            setTimeout(() => {
                btnAdd.textContent = originalText;
                btnAdd.style.background = '';
                btnAdd.style.color = '';
            }, 1800);
        }

        // Nút Yêu thích (Tim)
        const btnHeart = e.target.closest('.mavn-btn-heart');
        if (btnHeart) {
            e.preventDefault(); // Ngăn việc bị nhảy trang nếu bọc trong thẻ <a>
            const isLiked = btnHeart.classList.contains('liked');
            btnHeart.classList.toggle('liked');
            btnHeart.textContent = isLiked ? '♡' : '♥';
            btnHeart.style.color = isLiked ? '' : '#C45C3A';
        }

        // Chọn Size / Màu sắc
        const sizeBtn = e.target.closest('.size-btn');
        if (sizeBtn) sizeBtn.classList.toggle('active');

        const swatchBtn = e.target.closest('#filter-colors .swatch');
        if (swatchBtn) {
            swatchBtn.parentElement.querySelectorAll('.swatch').forEach(x => x.classList.remove('active'));
            swatchBtn.classList.add('active');
        }
    });

    // Ẩn vùng "Vừa xem" nếu không có sản phẩm
    const recentlyGrid = document.getElementById('recently-grid');
    const recentlySection = document.getElementById('recently-viewed-section');
    if (recentlyGrid && recentlySection && !recentlyGrid.children.length) {
        recentlySection.style.display = 'none';
    }
}

/* ===================== COUNTDOWN ===================== */
function setupCountdown() {
    const el = document.getElementById('countdown');
    if (!el) return;
    const endTime = parseInt(el.getAttribute('data-end-time'), 10);
    if (!endTime) return;

    function tick() {
        const diff = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
        const h = String(Math.floor(diff / 3600)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
        const s = String(diff % 60).padStart(2, '0');
        
        el.textContent = h + ':' + m + ':' + s;
        if (diff > 0) setTimeout(tick, 1000);
    }
    tick();
}

/* ===================== FOOTER ===================== */
function setupFooter() {
    const footerYear = document.getElementById('footerYear');
    if (footerYear) footerYear.textContent = new Date().getFullYear();
}

function handleNewsletterSubmit() {
    const input = document.getElementById('footerEmail');
    if (!input || !input.value.includes('@')) { 
        alert("Email không hợp lệ"); 
        return false; // Chặn submit form
    }
    alert("Đăng ký nhận tin thành công!");
    input.value = '';
    return false; // Chặn reload trang, hoặc đổi thành true nếu có xử lý backend
}