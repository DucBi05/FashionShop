<?php
/* ========================================================
   KẾT NỐI DATABASE & QUERY SẢN PHẨM
======================================================== */
include('../config/config.php');

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$sort        = isset($_GET['sort'])        ? $_GET['sort']              : 'default';
$page        = isset($_GET['page'])        ? max(1, (int)$_GET['page']) : 1;
$min_price = isset($_GET['min_price'])
  ? (int)$_GET['min_price']
  : 0;

$max_price = isset($_GET['max_price'])
  ? (int)$_GET['max_price']
  : 2000000;
$limit       = 12;
$offset      = ($page - 1) * $limit;

$total_sql = "SELECT COUNT(*) as total FROM products WHERE 1";
if ($min_price > 0)
{
  $total_sql .= " AND (price * (1 - discount_percent / 100)) >= $min_price";
}

if ($max_price > 0)
{
  $total_sql .= " AND (price * (1 - discount_percent / 100)) <= $max_price";
}
$total_all_result = mysqli_query(
  $conn,
  $total_sql
);

$total_all_products =
    mysqli_fetch_assoc($total_all_result)['total'];

$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1";
if ($category_id > 0)
{
  $count_sql .= " AND category_id = $category_id";
}

if ($min_price > 0)
{
  $count_sql .= " AND (price * (1 - discount_percent / 100)) >= $min_price";
}

if ($max_price > 0)
{
  $count_sql .= " AND (price * (1 - discount_percent / 100)) <= $max_price";
}
$count_result  = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages   = ceil($total_records / $limit);

$sql = "SELECT id, name, price, image, description, stock, sold, discount_percent, badge, rating, review_count FROM products WHERE 1";
if ($category_id > 0)
{
  $sql .= " AND category_id = $category_id";
}

if ($min_price > 0)
{
  $sql .= "
    AND
    (price * (1 - discount_percent / 100))
    >= $min_price
  ";
}

if ($max_price > 0)
{
  $sql .= "
    AND
    (price * (1 - discount_percent / 100))
    <= $max_price
  ";
}

switch ($sort) {

  case 'price_asc':

    $sql .= "
      ORDER BY
      (price * (1 - discount_percent / 100)) ASC
    ";

    break;

  case 'price_desc':

    $sql .= "
      ORDER BY
      (price * (1 - discount_percent / 100)) DESC
    ";

    break;

  case 'newest':

    $sql .= " ORDER BY created_at DESC";

    break;

  case 'discount':
    $sql .= " ORDER BY discount_percent DESC";
    break;

  case 'best_selling':
    $sql .= " ORDER BY sold DESC";
    break;

  case 'top_rated':
    $sql .= " ORDER BY rating DESC, review_count DESC";
    break;

  default:

    $sql .= " ORDER BY id DESC";

    break;
}
$sql .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

$priceCondition = "";

if($min_price > 0 || $max_price > 0)
{
  $priceCondition = "
    AND (p.price * (1 - p.discount_percent / 100))
    BETWEEN $min_price AND $max_price
  ";
}
$cat_result = mysqli_query($conn, "
  SELECT
    c.id,
    c.name,
    COUNT(p.id) as total_products
  FROM categories c
  LEFT JOIN products p
    ON c.id = p.category_id
    $priceCondition
  GROUP BY c.id
  ORDER BY c.name ASC
");
$categories = [];
while ($row = mysqli_fetch_assoc($cat_result)) {
  $categories[] = $row;
}

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

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/shop.css">


<div class="shop-container">

  <div class="shop-top-info">
    <div class="shop-breadcrumb">
      <a href="../index.php">Trang chủ</a> › Sản phẩm
    </div>
    <div class="shop-count"><?= $total_records ?> sản phẩm</div>
  </div>

  <div class="shop-body">
    <aside class="shop-sidebar" id="shop-sidebar">
      <h2 class="sidebar-title">Bộ lọc</h2>

      <div class="filter-group">
        <p class="filter-group__label">Danh mục</p>
        <ul class="filter-list">
          <li>
            <a href="shop.php?sort=<?= urlencode($sort) ?>&min_price=<?= $min_price ?>&max_price=<?= $max_price ?>" 
            class="<?= $category_id == 0 ? 'active' : '' ?>">
              Tất cả
              <span class="sp-cat-count"><?= $total_all_products ?></span>
            </a>
          </li>
          <?php foreach ($categories as $cat): ?>
          <?php

          $params = $_GET;
          $params['category_id'] = $cat['id'];
          unset($params['page']); // về trang 1 khi đổi danh mục

          ?>
          <li>
            <a href="shop.php?<?= http_build_query($params) ?>"
              class="<?= $category_id == $cat['id'] ? 'active' : '' ?>">
              <?= htmlspecialchars($cat['name']) ?>
              <span class="sp-cat-count">
                <?= $cat['total_products'] ?>
              </span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="filter-group">

        <p class="filter-group__label">
          Khoảng giá
        </p>

        <form method="GET">

          <?php if($category_id > 0): ?>
            <input type="hidden" name="category_id" value="<?= $category_id ?>">
          <?php endif; ?>

          <?php if($sort != 'default'): ?>
            <input type="hidden" name="sort" value="<?= $sort ?>">
          <?php endif; ?>

          <?php
          $min_price = $_GET['min_price'] ?? 0;
          $max_price = $_GET['max_price'] ?? 2000000;
          ?>

          <div class="range-slider">

            <div class="slider-track"></div>

            <input
              type="range"
              id="minRange"
              name="min_price"
              min="0"
              max="2000000"
              step="50000"
              value="<?= $min_price ?>">

            <input
              type="range"
              id="maxRange"
              name="max_price"
              min="0"
              max="2000000"
              step="50000"
              value="<?= $max_price ?>">

          </div>

          <div class="price-values">

            <span id="minPriceText">
              <?= number_format($min_price,0,',','.') ?>₫
            </span>

            <span id="maxPriceText">
              <?= number_format($max_price,0,',','.') ?>₫
            </span>

          </div>

          <button
            type="submit"
            class="price-filter-btn">

            Áp dụng

          </button>

        </form>

      </div>
    </aside>

    <div class="shop-main">

      <button class="btn-mobile-filter"
              onclick="document.getElementById('shop-sidebar').classList.toggle('open')">
        ⚙ Bộ lọc
      </button>

      <div class="shop-toolbar">
        <div class="toolbar-right">
          <form method="GET">
            <?php
            foreach($_GET as $key => $value)
            {
              if($key != 'sort' && $key != 'page')
              {
                echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
              }
            }
            ?>
            <select class="sort-select" name="sort" onchange="this.form.submit()">
              <option value="default"     <?= $sort=='default'     ? 'selected':'' ?>>Sắp xếp: Mặc định</option>
              <option value="price_asc"   <?= $sort=='price_asc'   ? 'selected':'' ?>>Giá: Thấp đến Cao</option>
              <option value="price_desc"  <?= $sort=='price_desc'  ? 'selected':'' ?>>Giá: Cao đến Thấp</option>
              <option value="newest"      <?= $sort=='newest'      ? 'selected':'' ?>>Hàng mới nhất</option>
              <option value="discount"    <?= $sort=='discount'    ? 'selected':'' ?>>Giảm giá nhiều nhất</option>
              <option value="best_selling"<?= $sort=='best_selling'? 'selected':'' ?>>Bán chạy nhất</option>
              <option value="top_rated"   <?= $sort=='top_rated'   ? 'selected':'' ?>>Đánh giá cao nhất</option>
            </select>
          </form>
          <div class="view-btns">
            <button class="btn-view active" id="view-grid" title="Dạng lưới" onclick="setView('grid')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
              </svg>
            </button>
            <button class="btn-view" id="view-list" title="Dạng danh sách" onclick="setView('list')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div class="shop-product-grid" id="product-grid">

        <?php if (mysqli_num_rows($result) == 0): ?>
          <div class="empty-state">
            <p>Không tìm thấy sản phẩm phù hợp</p>
            <small>Thử thay đổi bộ lọc hoặc danh mục khác</small>
          </div>
        <?php else: ?>
          <?php while ($p = mysqli_fetch_assoc($result)): ?>
            <a
              href="product_detail.php?id=<?= $p['id'] ?>"
              class="product-card-link">

              <div class="product-card">
                <div class="sp-card-img">

                  <?php if($p['discount_percent'] > 0): ?>
                    <span class="sp-badge sale">−<?= $p['discount_percent'] ?>%</span>
                  <?php elseif($p['badge'] == 'new'): ?>
                    <span class="sp-badge new">Mới</span>
                  <?php elseif($p['badge'] == 'best'): ?>
                    <span class="sp-badge best">Bán chạy</span>
                  <?php endif; ?>

                  <button
                    class="sp-heart <?= in_array($p['id'],$wishlist_ids) ? 'active' : '' ?>"
                    data-product-id="<?= $p['id'] ?>">

                    <?= in_array($p['id'],$wishlist_ids) ? '♥' : '♡' ?>

                  </button>

                  <?php if($p['image']): ?>

                    <div class="product-image-wrapper">

                      <img
                        src="../assets/uploads/products/<?= htmlspecialchars($p['image']) ?>"
                        alt="<?= htmlspecialchars($p['name']) ?>"
                        class="product-image">

                    </div>

                  <?php else: ?>

                    <div class="sp-card-img-placeholder">

                      <i class="fa-solid fa-image"></i>
                      <span>No Image</span>

                    </div>

                  <?php endif; ?>
                  
                  <div class="sp-card-overlay">
                    <button
                      class="sp-quick-add"
                      data-product-id="<?= $p['id'] ?>">

                      Thêm vào giỏ

                    </button>
                  </div>
                </div>

                <div class="product-info">

                  <h3 class="product-name">
                    <?= htmlspecialchars($p['name']) ?>
                  </h3>

                  <div class="product-description">

                    <?= htmlspecialchars(
                      mb_strimwidth(
                        $p['description'] ?? '',
                        0,
                        120,
                        '...'
                      )
                    ) ?>

                  </div>

                  <div class="product-price">

                    <?php if($p['discount_percent'] > 0):
                      $old_price = $p['price'];
                      $new_price = $p['price'] * (1 - $p['discount_percent'] / 100);
                    ?>
                      <span class="price-current on-sale">
                        <?= number_format($new_price, 0, ',', '.') ?>₫
                      </span>
                      <span class="price-old"><?= number_format($old_price, 0, ',', '.') ?>₫</span>
                    <?php else: ?>
                      <span class="price-current"><?= number_format($p['price'], 0, ',', '.') ?>₫</span>
                    <?php endif; ?>

                  </div>
                  
                  <?php if($p['rating'] > 0): ?>
                  <div style="display:flex; align-items:center; gap:5px; margin-top:2px;">
                    <span class="mavn-stars">
                      <?php
                      $full  = floor($p['rating']);
                      $empty = 5 - $full;
                      echo str_repeat('★', $full) . str_repeat('☆', $empty);
                      ?>
                    </span>
                    <span class="review-count">(<?= $p['review_count'] ?>)</span>
                  </div>
                  <?php endif; ?>

                  <?php if($p['stock'] > 0 && $p['stock'] <= 5): ?>
                    <span class="stock-low">Còn <?= $p['stock'] ?> sản phẩm</span>
                  <?php endif; ?>

                </div>
              </div>
            </a>
          <?php endwhile; ?>
        <?php endif; ?>

      </div>

      <?php if ($total_pages > 1): ?>
      <nav class="mavn-pagination" aria-label="Phân trang">
        <?php

        $params = $_GET;

        for($i = 1; $i <= $total_pages; $i++):

          $params['page'] = $i;

        ?>

        <a
          href="shop.php?<?= http_build_query($params) ?>"
          class="page-link <?= $i == $page ? 'active' : '' ?>">

          <?= $i ?>

        </a>

        <?php endfor; ?>
      </nav>
      <?php endif; ?>
          
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
function setView(mode) {

  localStorage.setItem(
    'product_view',
    mode
  );

  const grid = document.getElementById('product-grid');
  if (!grid) return;

  document.getElementById('view-grid').classList.toggle('active', mode === 'grid');
  document.getElementById('view-list').classList.toggle('active', mode === 'list');

  if (mode === 'list') {
    grid.classList.add('shop-product-list');
  } else {
    grid.classList.remove('shop-product-list');
  }
}

window.addEventListener(
  'load',
  () => {

    const view =
      localStorage.getItem(
        'product_view'
      ) || 'grid';

    setView(view);
  }
);

document
.querySelectorAll('.sp-quick-add')
.forEach(btn => {

  btn.addEventListener('click', function(e){

    e.preventDefault();
    e.stopPropagation();

    const productId = this.dataset.productId;

    fetch('../cart/add_to_cart.php', {

      method: 'POST',

      headers: {
        'Content-Type':
        'application/x-www-form-urlencoded'
      },

      body:
        'product_id=' + productId

    })

    .then(res => res.json())

    .then(data => {

        if(data.status === 'login_required')
        {
          window.location.href =
            '../auth/auth.php';

          return;
        }

        if(data.status === 'success')
        {
          document
            .getElementById('cartBadge')
            .innerText =
            data.cart_count;

          showShopToast(
            '🛒 Đã thêm vào giỏ hàng'
          );
        }

    });

  });

});

document.querySelectorAll('.sp-heart').forEach(btn => {

  btn.addEventListener('click', function(e){

    e.preventDefault();
    e.stopPropagation();

    const productId =
      this.dataset.productId;

    fetch('../wishlist/toggle_wishlist.php', {

      method: 'POST',

      headers: {
        'Content-Type':
        'application/x-www-form-urlencoded'
      },

      body:
        'product_id=' + productId
    })

    .then(res => res.json())

    .then(data => {

      if(data.status === 'added')
      {
        this.innerHTML = '♥';
        this.classList.add('active');

        showShopToast(
          '❤️ Đã thêm vào danh sách yêu thích'
        );
      }

      if(data.status === 'removed')
      {
        this.innerHTML = '♡';
        this.classList.remove('active');

        showShopToast(
          '💔 Đã xóa khỏi danh sách yêu thích'
        );
      }

      if(data.status === 'login_required')
      {
        window.location.href =
        '../auth/auth.php';
      }

    });
  });
});

function showShopToast(message, type = 'success')
{
    const toast =
      document.getElementById('toast');

    toast.innerText = message;
    toast.className = '';

    if(type === 'error')
    {
      toast.classList.add('error');
    }

    toast.classList.add('show');

    setTimeout(() => {

      toast.classList.remove('show');

    }, 2500);
}

const minRange =
    document.getElementById('minRange');

const maxRange =
    document.getElementById('maxRange');

const minText =
    document.getElementById('minPriceText');

const maxText =
    document.getElementById('maxPriceText');

function updatePriceRange()
{
    let min =
        parseInt(minRange.value);

    let max =
        parseInt(maxRange.value);

    if(min > max - 50000)
    {
        min =
            max - 50000;

        minRange.value =
            min;
    }

    if(max < min + 50000)
    {
        max =
            min + 50000;

        maxRange.value =
            max;
    }

    minText.innerText =
        min.toLocaleString('vi-VN') + '₫';

    maxText.innerText =
        max.toLocaleString('vi-VN') + '₫';
}

minRange.addEventListener(
    'input',
    updatePriceRange
);

maxRange.addEventListener(
    'input',
    updatePriceRange
);

updatePriceRange();
</script>

<?php include('../includes/footer.php'); ?>