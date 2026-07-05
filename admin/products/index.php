<?php

include("../includes/auth.php");
include("../../config/config.php");

$sql = "
    SELECT
        products.*,
        categories.name AS category_name

    FROM products

    LEFT JOIN categories
    ON products.category_id = categories.id
";

$keyword = trim($_GET['keyword'] ?? '');

if($keyword != '')
{
    $sql .= "
    WHERE products.name LIKE '%$keyword%'
    ";
}

$result = mysqli_query($conn, $sql);

$total_products = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM products")
)['total'];

$total_stock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(stock) total FROM products")
)['total'];

$out_stock = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total FROM products WHERE stock = 0"
    )
)['total'];
?>

<link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

<link
    rel="stylesheet"
    href="../assets/css/admin.css">

<div class="admin-layout">

    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">

        <?php include("../includes/header.php"); ?>

        <div class="table-card">

            <?php if(isset($_SESSION['success_message'])) { ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">

                    <?= $_SESSION['success_message'] ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" onclick="this.parentElement.remove();"></button>

                </div>

                <?php unset($_SESSION['success_message']); ?>

            <?php } ?>

            <div class="table-header">

                <h2>Products</h2>

                <div class="top-bar">

                    <form method="GET" class="search-form">

                        <input
                            type="text"
                            name="keyword"
                            placeholder="Search for products..."
                            value="<?= $_GET['keyword'] ?? '' ?>">

                        <button type="submit">
                            🔍
                        </button>

                    </form>

                    <a href="add.php" class="btn-dashboard-add">
                        + Add Product
                    </a>

                </div>

            </div>

            <div class="product-stats">

                <div class="stat-card">
                    <p>Total products</p>
                    <h3><?= $total_products ?></h3>
                </div>

                <div class="stat-card">
                    <p>Total stock</p>
                    <h3><?= $total_stock ?></h3>
                </div>

                <div class="stat-card danger">
                    <p>Out of stock</p>
                    <h3><?= $out_stock ?></h3>
                </div>

            </div>

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($product = mysqli_fetch_assoc($result)) { ?>

                        <tr>

                            <td>
                                #<?= $product['id'] ?>
                            </td>

                            <td>

                                <div class="product-cell">

                                    <img
                                        src="../../assets/uploads/products/<?= $product['image'] ?>"
                                        class="product-thumb">

                                    <div>
                                        <div class="product-title">
                                            <?= $product['name'] ?>
                                        </div>
                                    </div>

                                </div>

                            </td>

                            <td>
                                <?= number_format($product['price']) ?>đ
                            </td>

                            <td>
                                <?= $product['stock'] ?>
                            </td>

                            <td>
                                <?= $product['category_name'] ?>
                            </td>

                            <td>

                                <?php if($product['stock'] > 0): ?>

                                    <span class="status-badge in-stock">
                                        In Stock
                                    </span>

                                <?php else: ?>

                                    <span class="status-badge out-stock">
                                        Out Stock
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="edit.php?id=<?= $product['id'] ?>"
                                        class="btn-action btn-edit">

                                        Edit

                                    </a>

                                    <button
                                        type="button"
                                        class="btn-action btn-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="<?= $product['id'] ?>"
                                        data-name="<?= $product['name'] ?>">

                                        Delete

                                    </button>
                                </div>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">Xác nhận xóa</h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body">

                            Bạn có chắc muốn xóa sản phẩm "<span id="deleteProductName"></span>" không?

                            Hành động này không thể hoàn tác.

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>

                            <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Xóa</a>

                        </div>

                    </div>

                </div>

            </div>

            <?php include("../includes/footer.php"); ?>

        </div>

    </div>

</div>

<script>

const deleteModal = document.getElementById('deleteModal');

deleteModal.addEventListener('show.bs.modal', function (event) {

    const button = event.relatedTarget;

    const productId = button.getAttribute('data-id');
    const productName = button.getAttribute('data-name');

    document.getElementById('deleteProductName').textContent = productName;

    document.getElementById('confirmDeleteBtn').href = "delete.php?id=" + productId;

});

</script>

</html>