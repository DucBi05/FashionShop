<?php
include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="section">
<h1 style="font-size:1.75rem;font-weight:800;margin-bottom:1.5rem">🛍️ Cửa hàng</h1>
<!-- Filter bar -->
<div class="filter-bar">
    <div class="filter-group">
    <label>Tìm kiếm</label>
    <input type="text" id="shopSearch" placeholder="Tên sản phẩm..." oninput="applyFilters()">
    </div>
    <div class="filter-group">
    <label>Danh mục</label>
    <select id="catFilter" onchange="applyFilters()">
        <option value="">Tất cả</option>
        <option>Áo</option><option>Quần</option><option>Váy</option>
        <option>Túi</option><option>Giày</option><option>Phụ kiện</option>
    </select>
    </div>
    <div class="filter-group">
    <label>Giá tối đa (nghìn đ)</label>
    <input type="number" id="priceFilter" placeholder="Không giới hạn" oninput="applyFilters()">
    </div>
    <div style="display:flex;flex-direction:column;gap:.35rem">
    <label style="font-size:.75rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Lọc nhanh</label>
    <div class="filter-tags">
        <span class="ftag" onclick="toggleTag(this,'sale')" data-tag="sale">🔥 Sale</span>
        <span class="ftag" onclick="toggleTag(this,'new')" data-tag="new">✨ Mới</span>
        <span class="ftag" onclick="toggleTag(this,'hot')" data-tag="hot">⭐ Bán chạy</span>
    </div>
    </div>
</div>
<div class="sort-row">
    <span class="result-count" id="resultCount">Hiển thị 0 sản phẩm</span>
    <select class="sort-select" id="sortSelect" onchange="applyFilters()">
    <option value="default">Mặc định</option>
    <option value="price-asc">Giá tăng dần</option>
    <option value="price-desc">Giá giảm dần</option>
    <option value="name-asc">Tên A–Z</option>
    <option value="name-desc">Tên Z–A</option>
    <option value="rating">Đánh giá cao nhất</option>
    </select>
</div>
<div class="products-grid" id="shopGrid"></div>
</div>

<?php
include("../includes/footer.php");
?>