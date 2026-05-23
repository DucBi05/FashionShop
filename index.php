<?php
include("includes/navbar.php");
include("config/config.php");

$sql = "
    SELECT
        categories.id,
        categories.name,
        categories.icon,
        COUNT(products.id) AS total_products

    FROM categories

    LEFT JOIN products
    ON categories.id = products.category_id

    GROUP BY categories.id
";

$result = mysqli_query($conn, $sql);

$sql = "
    SELECT *
    FROM products
    ORDER BY created_at DESC
    LIMIT 4
";

$featured_products = mysqli_query($conn, $sql);

$sql = "
    SELECT *
    FROM products
    ORDER BY created_at DESC
    LIMIT 4
";

$new_result = mysqli_query($conn, $sql);

?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="page active" id="page-home">
  <!-- Hero -->
  <div class="hero">
    <div class="hero-content">
      <h1>Thời Trang <span>Đỉnh Cao</span><br>Phong Cách Của Bạn</h1>
      <p>Khám phá bộ sưu tập thời trang & phụ kiện mới nhất. Đẳng cấp – Hiện đại – Độc đáo.</p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <button class="btn btn-primary" onclick="showPage('shop')">Mua ngay 🛍️</button>
        <button class="btn btn-outline" onclick="scrollToSection('featured')">Xem bộ sưu tập</button>
      </div>
      <div class="hero-badges">
        <span class="hero-badge">✅ Hàng chính hãng</span>
        <span class="hero-badge">🚚 Giao trong 2h</span>
        <span class="hero-badge">↩️ Đổi trả 30 ngày</span>
        <span class="hero-badge">💳 Thanh toán an toàn</span>
      </div>
    </div>
  </div>

<!-- Categories -->
<div class="section">

    <div class="section-header">

        <h2 class="section-title">
            Danh mục <span>nổi bật</span>
        </h2>

    </div>

    <div class="categories-grid">

        <?php while($category = mysqli_fetch_assoc($result)) { ?>

            <a
                href="products/shop.php?category_id=<?= $category['id'] ?>"
                class="cat-card-link">

                <div class="cat-card">

                    <div class="cat-icon">
                        <?= $category['icon'] ?>
                    </div>

                    <div class="cat-name">
                        <?= $category['name'] ?>
                    </div>

                    <div class="cat-count">
                        <?= $category['total_products'] ?> sản phẩm
                    </div>

                </div>

            </a>

        <?php } ?>

    </div>

</div>

<!-- Featured -->
<div class="section" id="featured">

    <div class="section-header">

        <h2 class="section-title">
            Sản phẩm <span>nổi bật</span>
        </h2>

        <a
            href="products/shop.php"
            class="see-all">

            Xem tất cả →

        </a>

    </div>

    <div class="products-grid">

        <?php while($product = mysqli_fetch_assoc($featured_products)) { ?>

            <div class="product-card">

                <div class="product-image">

                    <img
                        src="assets/uploads/products/<?= $product['image'] ?>"
                        alt="<?= $product['name'] ?>">

                </div>

                <div class="product-info">

                    <h3 class="product-name">
                        <?= $product['name'] ?>
                    </h3>

                    <p class="product-price">
                        <?= number_format($product['price']) ?>đ
                    </p>

                    <div class="product-actions">

                        <a
                            href="products/product_detail.php?id=<?= $product['id'] ?>"
                            class="btn-view">

                            Xem chi tiết

                        </a>

                        <button
                            class="btn-cart"
                            onclick="addToCart(<?= $product['id'] ?>)">

                            🛒

                        </button>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

  <!-- Sale Section -->
  <div style="background:linear-gradient(135deg,var(--accent),#c73650);padding:3rem 1rem;text-align:center;color:#fff;margin:2rem 0">
    <h2 style="font-size:2rem;margin-bottom:0.5rem">🔥 FLASH SALE HÔM NAY</h2>
    <p style="margin-bottom:1.5rem;opacity:.9">Giảm đến 50% – Chỉ còn <strong id="countdown">02:45:30</strong></p>
    <button class="btn" style="background:#fff;color:var(--accent)" onclick="showPage('shop')">Xem ưu đãi ngay</button>
  </div>

<!-- New Arrivals -->
<div class="section">

    <div class="section-header">

        <h2 class="section-title">
            Hàng <span>mới về</span>
        </h2>

        <a
            href="products/shop.php"
            class="see-all">

            Xem tất cả →

        </a>

    </div>

    <div class="products-grid">

        <?php while($product = mysqli_fetch_assoc($new_result)) { ?>

            <div class="product-card">

                <!-- Product Image -->
                <div class="product-image">

                    <img
                        src="assets/uploads/products/<?= $product['image'] ?>"
                        alt="<?= $product['name'] ?>">

                    <!-- NEW BADGE -->
                    <span class="new-badge">
                        NEW
                    </span>

                </div>

                <!-- Product Info -->
                <div class="product-info">

                    <h3 class="product-name">
                        <?= $product['name'] ?>
                    </h3>

                    <p class="product-price">
                        <?= number_format($product['price']) ?>đ
                    </p>

                    <div class="product-actions">

                        <a
                            href="products/product_detail.php?id=<?= $product['id'] ?>"
                            class="btn-view">

                            Xem chi tiết

                        </a>

                        <button
                            class="btn-cart"
                            onclick="addToCart(<?= $product['id'] ?>)">

                            🛒

                        </button>

                    </div>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<?php
include("includes/footer.php");
?>