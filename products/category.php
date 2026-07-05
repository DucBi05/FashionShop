<?php
/* ========================================================
   KẾT NỐI DATABASE & QUERY DANH MỤC
======================================================== */
// Gọi file cấu hình chung của toàn dự án (giống file shop.php)
include('../config/config.php');

// Truy vấn lấy danh sách danh mục và đếm tổng số sản phẩm trong mỗi danh mục
$sql = "
    SELECT c.id, c.name, COUNT(p.id) as total_products 
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.name
    ORDER BY c.name ASC
";

$result = mysqli_query($conn, $sql);

$categories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

/* ========================================================
   HIỂN THỊ GIAO DIỆN
======================================================== */
include('../includes/navbar.php');
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/category.css">

<main class="category-page">
    <div class="category-header">
        <h1 class="category-title">Bộ Sưu Tập</h1>
        <p class="category-subtitle">Khám phá các dòng sản phẩm thời trang nổi bật của chúng tôi</p>
    </div>

    <div class="category-grid">
        <a href="shop.php" class="category-card">
            <h2 class="category-name">Tất cả sản phẩm</h2>
            <span class="category-count">Xem toàn bộ</span>
        </a>

        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <a href="shop.php?category_id=<?= $cat['id'] ?>" class="category-card">
                    <h2 class="category-name"><?= htmlspecialchars($cat['name']) ?></h2>
                    <span class="category-count">
                        <?= $cat['total_products'] ?> sản phẩm
                    </span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; width: 100%; color: #9C8F85;">Hiện chưa có danh mục nào.</p>
        <?php endif; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>