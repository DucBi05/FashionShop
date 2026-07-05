<?php
/* ========================================================
   KẾT NỐI DATABASE & XỬ LÝ TÌM KIẾM
======================================================== */
include('../config/config.php');

// Nhận từ khóa từ URL (ví dụ: search.php?q=áo)
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];
$total_results = 0;

if (!empty($keyword)) {
    // Escaping chuỗi để chống SQL Injection khi dùng mysqli
    $safe_keyword = mysqli_real_escape_string($conn, $keyword);
    $searchTerm = "%" . $safe_keyword . "%";

    // ĐÃ SỬA: Khớp câu lệnh SQL và xóa các cột không có trong DB của bạn
    $sql = "SELECT id, name, price, image FROM products 
            WHERE name LIKE '$searchTerm' 
            ORDER BY id DESC";
            
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $total_results = mysqli_num_rows($result);
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
}

/* ========================================================
   HIỂN THỊ GIAO DIỆN
======================================================== */
include('../includes/navbar.php');
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/shop.css"> 
<link rel="stylesheet" href="../assets/css/search.css">

<main class="search-page">
    <div class="search-header">
        <?php if (!empty($keyword)): ?>
            <h1>Kết quả tìm kiếm</h1>
            <p>Tìm thấy <?= $total_results ?> sản phẩm cho từ khóa <strong>"<?= htmlspecialchars($keyword) ?>"</strong></p>
        <?php else: ?>
            <h1>Tìm kiếm sản phẩm</h1>
            <p>Vui lòng nhập từ khóa để tìm kiếm.</p>
        <?php endif; ?>
    </div>

    <div class="shop-product-grid">
        
        <?php if (empty($keyword)): ?>
            <div class="empty-state">
                <p>Bạn chưa nhập từ khóa tìm kiếm nào.</p>
                <small>Sử dụng thanh tìm kiếm trên menu để bắt đầu.</small>
            </div>

        <?php elseif ($total_results == 0): ?>
            <div class="empty-state">
                <p>Không tìm thấy sản phẩm nào khớp với từ khóa "<?= htmlspecialchars($keyword) ?>".</p>
                <small>Hãy thử lại với các từ khóa ngắn gọn hoặc chung chung hơn (ví dụ: váy, áo thun...).</small>
            </div>

        <?php else: ?>
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                
                <a href="product_detail.php?id=<?= $p['id'] ?>">
                    <img class="product-img" 
                         src="assets/img/products/<?= htmlspecialchars($p['image']) ?>" 
                         alt="<?= htmlspecialchars($p['name']) ?>">
                </a>

                <div class="product-info">
                    <h3 class="product-name">
                        <a href="product_detail.php?id=<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                    </h3>
                    
                    <div class="product-price">
                        <span class="price-current">
                            <?= number_format($p['price'], 0, ',', '.') ?>₫
                        </span>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</main>

<?php include('../includes/footer.php'); ?>