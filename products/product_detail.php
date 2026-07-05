<?php
/* ========================================================
   KẾT NỐI DATABASE & QUERY SẢN PHẨM
======================================================== */
include('../config/config.php');

// Nhận tham số từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<script>alert('ID không hợp lệ!'); window.location.href='shop.php';</script>";
    exit;
}

// Truy vấn chi tiết sản phẩm
$sql = "SELECT * FROM products WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='shop.php';</script>";
    exit;
}

$product = mysqli_fetch_assoc($result);

// Xử lý logic số sao (Mặc định 0 nếu không có)
$rating = isset($product['rating']) ? round($product['rating']) : 0;
$stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);

// Xử lý Size & Color (Giả sử trong database lưu dạng chuỗi cách nhau bằng dấu phẩy)
$sizes = !empty($product['size']) ? explode(',', $product['size']) : [];
$colors = !empty($product['color']) ? explode(',', $product['color']) : [];

$wishlist_ids = [];

if(isset($_SESSION['user_id']))
{
  $uid = $_SESSION['user_id'];

  $rs = mysqli_query(
    $conn,
    "
    SELECT product_id
    FROM wishlist
    WHERE user_id = $uid
    "
  );

  while($row = mysqli_fetch_assoc($rs))
  {
    $wishlist_ids[] = $row['product_id'];
  }
}

/* ========================================================
   HIỂN THỊ GIAO DIỆN
======================================================== */
include('../includes/navbar.php');
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/product_detail.css">

<main>
  <div class="detail-container">
    
    <div class="detail-image-wrapper">
      <img src="../assets/uploads/products/<?= htmlspecialchars($product['image']) ?>" 
        alt="<?= htmlspecialchars($product['name']) ?>"
        class="detail-image">
    </div>

    <div class="detail-info">
      
      <?php if (!empty($product['badge'])): ?>
        <span class="detail-badge"><?= htmlspecialchars($product['badge']) ?></span>
      <?php elseif ($product['discount_percent'] > 0): ?>
        <span class="detail-badge">Giảm giá <?= htmlspecialchars($product['discount_percent']) ?>%</span>
      <?php endif; ?>
      
      <h1 class="detail-name"><?= htmlspecialchars($product['name']) ?></h1>
      
      <div style="display:flex; align-items:center; gap:5px; margin-top:2px;">

        <div class="detail-rating"><?= $stars ?></div>
        <span class="review-count">(<?= $product['rating'] ?>★)</span>
        <span class="review-count">(<?= $product['review_count'] ?> lượt đánh giá)</span>
        
      </div>
      
      <div class="detail-price-wrap">
        <?php if($product['discount_percent'] > 0):
          $old_price = $product['price'];
          $new_price = $product['price'] * (1 - $product['discount_percent'] / 100);
        ?>
          <span class="detail-price on-sale">
            <?= number_format($new_price, 0, ',', '.') ?>đ
          </span>
          <span class="detail-old-price">
            <?= number_format($old_price, 0, ',', '.') ?>đ
          </span>
        <?php else: ?>
          <span class="detail-price on-sale">
            <?= number_format($product['price'], 0, ',', '.') ?>đ
          </span>
        <?php endif; ?>
      </div>

      <div class="product-stock">

        <?php if($product['stock'] > 10): ?>

          <span class="stock-in">
            ✓ Còn hàng
          </span>

        <?php elseif($product['stock'] > 0): ?>

          <span class="stock-low">
            ⚠ Chỉ còn <?= $product['stock'] ?> sản phẩm
          </span>

        <?php else: ?>

          <span class="stock-out">
            ✖ Hết hàng
          </span>

        <?php endif; ?>

      </div>
      
      <div class="detail-desc">
        <?= nl2br(htmlspecialchars($product['description'] ?? 'Sản phẩm này chưa có mô tả.')) ?>
      </div>
      
      <form id="addToCartForm">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <div class="quantity-box">

          <label>Số lượng</label>

          <div class="quantity-control">

            <button type="button" onclick="changeQuantity(-1)">−</button>

            <input
              type="number"
              id="quantity"
              name="quantity"
              value="1"
              min="1"
              max="<?= $product['stock'] ?>">

            <button type="button" onclick="changeQuantity(1)">+</button>

          </div>

        </div>
        <?php if($product['stock'] > 0): ?>

        <button
          type="submit"
          class="btn-add-cart"
          id="btn-add">

          <span class="btn-text">
            THÊM VÀO GIỎ HÀNG
          </span>

        </button>

        <?php else: ?>

        <button
          type="button"
          class="btn-add-cart disabled"
          disabled>

          HẾT HÀNG

        </button>

        <?php endif; ?>
      </form>

      <div class="detail-actions">

        <button
          class="btn-wishlist <?= in_array($product['id'],$wishlist_ids) ? 'active' : '' ?>"
          data-product-id="<?= $product['id'] ?>">

          <span class="heart-icon">❤</span>
          Yêu thích

        </button>

      </div>

    </div>
  </div>
  <?php

  $related_sql = "
    SELECT *
    FROM products
    WHERE category_id = {$product['category_id']}
    AND id != {$product['id']}
    ORDER BY RAND()
    LIMIT 4
  ";

  $related_result = mysqli_query(
    $conn,
    $related_sql
  );

  ?>

  <div class="related-section">

    <h2>Sản phẩm liên quan</h2>

    <div class="related-grid">

      <?php while($item = mysqli_fetch_assoc($related_result)): ?>

        <a
          href="product_detail.php?id=<?= $item['id'] ?>"
          class="related-card">

          <img
            src="../assets/uploads/products/<?= $item['image'] ?>"
            alt="<?= htmlspecialchars($item['name']) ?>">

          <h4>
            <?= htmlspecialchars($item['name']) ?>
          </h4>

          <p>
            <?= number_format($item['price'],0,',','.') ?>đ
          </p>

        </a>

      <?php endwhile; ?>

    </div>

  </div>
</main>

<script>
  document
  .getElementById('addToCartForm')
  .addEventListener('submit', function(e){

    e.preventDefault();

    const form = this;
    const btn  = document.getElementById('btn-add');

    btn.disabled = true;
    btn.innerHTML = 'ĐANG THÊM...';

    fetch('../cart/add_to_cart.php', {
      method: 'POST',
      body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {

      if(data.status === 'login_required')
      {
        window.location.href =
          '../auth/auth.php';

        return;
      }

      if(data.status === 'success'){

        btn.innerHTML = '✓ ĐÃ THÊM VÀO GIỎ';
        btn.classList.add('added');

        document
          .getElementById('cartBadge')
          .innerText =
          data.cart_count;

        setTimeout(() => {

          btn.innerHTML = 'THÊM VÀO GIỎ HÀNG';
          btn.classList.remove('added');
          btn.disabled = false;

        }, 2000);

      }else{

        btn.innerHTML = 'THÊM VÀO GIỎ HÀNG';
        btn.disabled = false;

        showToast(data.message || 'Có lỗi xảy ra');
      }

    })
    .catch(() => {

      btn.innerHTML = 'THÊM VÀO GIỎ HÀNG';
      btn.disabled = false;

      showToast('Không thể kết nối máy chủ');

    });

  });

  function changeQuantity(value)
  {
    const input = document.getElementById('quantity');

    let qty = parseInt(input.value) || 1;

    qty += value;

    if(qty < 1) qty = 1;

    if(qty > <?= $product['stock'] ?>)
    {
      qty = <?= $product['stock'] ?>;
    }

    input.value = qty;
  }

  document.querySelector('.btn-wishlist')
  ?.addEventListener('click', function(){

    const productId = this.dataset.productId;

    fetch('../wishlist/toggle_wishlist.php', {

      method:'POST',

      headers: {
        'Content-Type':
        'application/x-www-form-urlencoded'
      },

      body:'product_id=' + productId

    })
    .then(res => res.json())
    .then(data => {

      if(data.status === 'added')
      {
        this.classList.toggle('active');
      }

      if(data.status === 'removed')
      {
        this.classList.remove('active');
      }

      if(data.status === 'login_required')
      {
        window.location.href =
        '../auth/auth.php';
      }

    });

  });
</script>

<?php include('../includes/footer.php'); ?>