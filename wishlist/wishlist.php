<?php

session_start();
include("../config/config.php");

if(!isset($_SESSION['user_id']))
{
    header("Location: ../auth/auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT
    p.*
FROM wishlist w
JOIN products p
ON w.product_id = p.id
WHERE w.user_id = $user_id
ORDER BY w.id DESC
";

$result = mysqli_query($conn, $sql);

include("../includes/navbar.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/wishlist.css">

<div class="wishlist-container">

    <div class="wishlist-header">

        <h1>❤️ Sản phẩm yêu thích</h1>

        <span id="wishlistCount">
            <?= mysqli_num_rows($result) ?> sản phẩm
        </span>

    </div>

    <div class="empty-wishlist" id="emptyWishlist" style="<?= mysqli_num_rows($result) > 0 ? 'display:none' : '' ?>">

        <div class="empty-icon">💔</div>

        <h3>Chưa có sản phẩm yêu thích</h3>

        <p>Hãy thêm những sản phẩm bạn thích.</p>

        <a href="../products/shop.php" class="btn-shop">
            Thêm ngay
        </a>

    </div>

    <div class="wishlist-grid" id="wishlistGrid" style="<?= mysqli_num_rows($result) == 0 ? 'display:none;' : '' ?>">

        <?php while($p = mysqli_fetch_assoc($result)): ?>

        <div class="wishlist-card" data-product-id="<?= $p['id'] ?>">

            <a href="../products/product_detail.php?id=<?= $p['id'] ?>">

                <div class="wishlist-image">

                    <?php if(!empty($p['image'])): ?>

                    <img
                        src="../assets/uploads/products/<?= $p['image'] ?>"
                        alt="<?= htmlspecialchars($p['name']) ?>">

                    <?php else: ?>

                    <div class="no-image">
                        No Image
                    </div>

                    <?php endif; ?>

                </div>

            </a>

            <div class="wishlist-info">

                <h3>
                    <?= htmlspecialchars($p['name']) ?>
                </h3>

                <?php
                $price = $p['price'];

                if($p['discount_percent'] > 0)
                {
                    $price =
                        $price *
                        (1 - $p['discount_percent']/100);
                }
                ?>

                <div class="wishlist-price">

                    <?= number_format($price) ?>đ

                </div>

                <div class="wishlist-actions">

                    <button
                        class="btn-cart"
                        onclick="addToCartFromWishLish(<?= $p['id'] ?>)">

                        Thêm vào giỏ

                    </button>

                    <button
                        class="btn-remove"
                        onclick="removeWishlist(<?= $p['id'] ?>)">

                        Xóa

                    </button>

                </div>

            </div>

        </div>

        <?php endwhile; ?>

    </div>

</div>

<div id="toast"></div>

<script>

function addToCartFromWishLish(productId){

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

        if(data.status === 'success')
        {
        document
            .getElementById('cartBadge')
            .innerText =
            data.cart_count;

        showWishToast(
            '🛒 Đã thêm vào giỏ hàng'
        );
        }

    });
}

function removeWishlist(productId){
    fetch('toggle_wishlist.php', {

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

        if (data.status !== 'removed') return;

        showWishToast(
          '💔 Đã xóa khỏi danh sách yêu thích'
        );

        const item = document.querySelector(
            `[data-product-id="${productId}"]`
        );

        if (!item) return;

        item.style.transition = ".3s";
        item.style.opacity = "0";
        item.style.transform = "scale(.95)";

        setTimeout(() => {

            item.remove();

            // cập nhật số lượng
            const grid = document.getElementById("wishlistGrid");
            const count = grid.querySelectorAll(".wishlist-card").length;

            document.getElementById("wishlistCount").innerText =
                `${count} sản phẩm`;

            // nếu hết sản phẩm
            if (count === 0) {

                grid.style.display = "none";

                document.getElementById("emptyWishlist").style.display = "block";
            }

        }, 300);

    });
}

function showWishToast(message, type = 'success')
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

</script>

<?php include("../includes/footer.php"); ?>