<?php
include_once(__DIR__ . "/../config/config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = $isLoggedIn ? $_SESSION['user'] : null;

$cart_count = 0;

if(isset($_SESSION['user_id']))
{
  $uid = $_SESSION['user_id'];

  $rs = mysqli_query(
    $conn,
    "
    SELECT SUM(quantity) total
    FROM cart
    WHERE session_id = $uid
    "
  );

  $cart_count =
    mysqli_fetch_assoc($rs)['total']
    ?? 0;
}
?>

<nav class="nav">
  <div class="nav-inner">

    <div class="logo">
      Fashion<span>Shop</span>
    </div>

    <div class="nav-links">

      <a href="<?= $base_url ?>index.php">
        🏠 Trang chủ
      </a>

      <a href="<?= $base_url ?>products/shop.php">
        🛍️ Cửa hàng
      </a>

      <a href="<?= $base_url ?>contact/contact.php">
        📍 Liên hệ
      </a>

      <a href="<?= $base_url ?>policy/policy.php">
        📋 Chính sách
      </a>
    </div>

    <div class="search-bar">

      <span>🔍</span>

      <input
        type="text"
        id="globalSearch"
        placeholder="Tìm kiếm sản phẩm..."
        oninput="globalSearchHandler(this.value)">

    </div>

    <div class="nav-right">

      <?php if ($isLoggedIn): ?>
        <!-- Đã đăng nhập: hiện tên + dropdown -->
        <div class="user-dropdown" id="userDropdown">
          <button class="nav-user-btn" onclick="toggleUserMenu()">
            <span class="nav-user-avatar">
              <?= $currentUser['avatar'] ? '<img src="' . $base_url . 'assets/uploads/users/' . $currentUser['avatar'] . '" alt="avatar">' 
              : mb_substr($currentUser['fullname'], 0, 1, 'UTF-8') ?>
            </span>
            <span class="nav-user-name"><?= htmlspecialchars($currentUser['fullname']) ?></span>
            <span class="dropdown-arrow">▾</span>
          </button>
          <div class="user-dropdown-menu" id="userDropdownMenu">
            <a href="<?= $base_url ?>profile.php" class="dropdown-item">
              📋 Hồ sơ của tôi
            </a>
            <a href="<?= $base_url ?>orders/my_orders.php" class="dropdown-item">
              📦 Đơn hàng
            </a>
            <a href="<?= $base_url ?>wishlist/wishlist.php" class="dropdown-item">
              ❤️ Yêu thích
            </a>
            <div class="dropdown-divider"></div>
            <a href="javascript:void(0)" class="dropdown-item" onclick="logoutFromNav()">
              🚪 Đăng xuất
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Chưa đăng nhập -->
        <a
          href="<?= $base_url ?>auth/auth.php"
          class="nav-icon"
          title="Đăng nhập">

          👤

        </a>
      <?php endif; ?>

      <a
        href="<?= $base_url ?>cart/cart.php"
        class="nav-icon"
        title="Giỏ hàng">

        🛒
        <span class="badge" id="cartBadge"><?= $cart_count ?></span>

      </a>

    </div>

  </div>
</nav>

<?php if ($isLoggedIn): ?>
<script>
function toggleUserMenu() {
  const menu = document.getElementById('userDropdownMenu');
  menu.classList.toggle('open');
}

// Đóng dropdown khi click ra ngoài
document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('userDropdown');
  if (dropdown && !dropdown.contains(e.target)) {
    document.getElementById('userDropdownMenu').classList.remove('open');
  }
});

async function logoutFromNav() {
  try {
    await fetch('<?= $base_url ?>api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'logout' })
    });
  } catch(e) {}
  window.location.href = '<?= $base_url ?>auth/auth.php';
}
</script>
<?php endif; ?>