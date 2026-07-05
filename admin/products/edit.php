<?php

include("../includes/auth.php");
include("../../config/config.php");

$id = $_GET['id'];

$product_sql = "
    SELECT *
    FROM products
    WHERE id = '$id'
";

$product_result = mysqli_query($conn, $product_sql);

$product = mysqli_fetch_assoc($product_result);

$category_sql = "SELECT * FROM categories";
$categories = mysqli_query($conn, $category_sql);

if(isset($_POST['update_product'])) {

    $category_id = $_POST['category_id'];
    $name        = $_POST['name'];
    $price       = $_POST['price'];
    $description = $_POST['description'];
    $stock       = $_POST['stock'];
    $discount_percent = $_POST['discount_percent'] ?? 0;
    $badge            = $_POST['badge'] ?? '';

    $image = $product['image'];

    if($_FILES['image']['name']) {

        $image = $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../assets/uploads/products/" . $image
        );
    }

    $sql = "
        UPDATE products

        SET
            category_id = '$category_id',
            name        = '$name',
            price       = '$price',
            image       = '$image',
            description = '$description',
            stock       = '$stock',
            discount_percent = '$discount_percent',
            badge            = '$badge'

        WHERE id = '$id'
    ";

    mysqli_query($conn, $sql);

    $_SESSION['success_message'] = "Cập nhật sản phẩm thành công!";

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Product</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="admin-layout">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">

        <?php include("../includes/header.php"); ?>

        <div class="table-card">

            <div class="table-border">

                <h2 class="mb-4">
                    Edit Product
                </h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>Product Name</label>

                        <input
                            type="text"
                            name="name"
                            value="<?= $product['name'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Price</label>

                        <input
                            type="number"
                            name="price"
                            value="<?= $product['price'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Category</label>

                        <select
                            name="category_id"
                            class="form-control">

                            <?php while($cat = mysqli_fetch_assoc($categories)) { ?>

                                <option
                                    value="<?= $cat['id'] ?>"

                                    <?= $cat['id'] == $product['category_id']
                                        ? 'selected'
                                        : '' ?>>

                                    <?= $cat['name'] ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>Discount (%)</label>

                            <input
                                type="number"
                                name="discount_percent"
                                class="form-control"
                                min="0"
                                max="100"
                                value="<?= $product['discount_percent'] ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>Badge</label>

                            <select
                                name="badge"
                                class="form-control">

                                <option value=""
                                    <?= $product['badge'] == ''
                                        ? 'selected'
                                        : '' ?>>
                                    Không có

                                <option value="new"
                                    <?= $product['badge'] == 'new'
                                        ? 'selected'
                                        : '' ?>>
                                    Mới
                                </option>

                                <option value="best"
                                    <?= $product['badge'] == 'best'
                                        ? 'selected'
                                        : '' ?>>
                                    Bán chạy
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-2">

                        <small id="discountPreview" class="text-success">

                            💰 Price after discount: <?= $product['price'] - ($product['price'] * $product['discount_percent'] / 100) ?>đ

                        </small>

                    </div>

                    <div class="mb-3">

                        <label>Stock</label>

                        <input
                            type="number"
                            name="stock"
                            value="<?= $product['stock'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Description</label>

                        <textarea
                            name="description"
                            class="form-control"><?=
                        htmlspecialchars(trim($product['description']))
                        ?></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Product Image

                        </label>

                        <div class="upload-area" id="uploadArea">

                            <input
                                type="file"
                                id="imageInput"
                                name="image"
                                accept="image/*"
                                hidden>

                            <img
                                src="../../assets/uploads/products/<?= $product['image'] ?>"
                                id="imagePreview"
                                class="preview-image">

                            <div class="upload-content">

                                <h5>
                                    Change Product Image
                                </h5>

                                <p>
                                    Drag & Drop image here or click to browse
                                </p>

                                <small id="fileInfo">

                                    Current image:
                                    <?= htmlspecialchars($product['image']) ?>

                                </small>

                            </div>

                        </div>

                    </div>

                    <button
                        type="submit"
                        name="update_product"
                        class="btn-product-save">

                        Update Product

                    </button>

                </form>

            </div>

            <?php include("../includes/footer.php"); ?>

        </div>

    </div>
    </div>

<script>

const uploadArea =
document.getElementById("uploadArea");

const imageInput =
document.getElementById("imageInput");

const imagePreview =
document.getElementById("imagePreview");

const fileInfo =
document.getElementById("fileInfo");

/* Click chọn ảnh */

uploadArea.addEventListener("click", () => {

    imageInput.click();

});

/* Chọn file */

imageInput.addEventListener("change", () => {

    previewFile(
        imageInput.files[0]
    );

});

/* Kéo vào */

uploadArea.addEventListener("dragover", (e) => {

    e.preventDefault();

    uploadArea.classList.add("dragover");

});

/* Kéo ra */

uploadArea.addEventListener("dragleave", () => {

    uploadArea.classList.remove("dragover");

});

/* Thả file */

uploadArea.addEventListener("drop", (e) => {

    e.preventDefault();

    uploadArea.classList.remove("dragover");

    const file = e.dataTransfer.files[0];

    imageInput.files = e.dataTransfer.files;

    previewFile(file);

});

function previewFile(file)
{
    if(!file) return;

    const reader = new FileReader();

    reader.onload = function(e)
    {
        imagePreview.src = e.target.result;
    }

    reader.readAsDataURL(file);

    const size =
        (file.size / 1024 / 1024)
        .toFixed(2);

    fileInfo.innerHTML =

        "Selected: " +

        file.name +

        " (" +

        size +

        " MB)";
}

const priceInput = document.querySelector('input[name="price"]');
const discountInput = document.querySelector('input[name="discount_percent"]');
const preview = document.getElementById('discountPreview');

function updateDiscountPrice()
{
    const price = parseFloat(priceInput.value || 0);
    const discount = parseFloat(discountInput.value || 0);

    const finalPrice =
        price * (1 - discount / 100);

    preview.innerHTML =
        '💰 Price after discount: ' +
        finalPrice.toLocaleString('vi-VN') +
        'đ';
}

priceInput.addEventListener('input', updateDiscountPrice);
discountInput.addEventListener('input', updateDiscountPrice);

</script>

</body>
</html>