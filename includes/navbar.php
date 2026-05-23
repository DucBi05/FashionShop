<?php
include_once(__DIR__ . "/../config/config.php");
?>

<nav class="nav">
  <div class="nav-inner">

    <div class="logo">
      Style<span>Vibe</span>
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

      <a
        href="<?= $base_url ?>auth/auth.php"
        class="nav-icon"
        title="Đăng nhập">

        👤

      </a>

      <a
        href="<?= $base_url ?>cart/cart.php"
        class="nav-icon"
        title="Giỏ hàng">

        🛒
        <span class="badge" id="cartBadge">0</span>

      </a>

    </div>

  </div>
</nav>