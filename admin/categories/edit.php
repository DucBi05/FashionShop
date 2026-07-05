<?php

include("../../config/config.php");

$id = $_GET['id'];

$category_sql = "
    SELECT *
    FROM categories
    WHERE id = '$id'
";

$category_result = mysqli_query($conn, $category_sql);

$category = mysqli_fetch_assoc($category_result);

if(isset($_POST['update_category'])) {

    $name = $_POST['name'];
    $icon = $_POST['icon'];

    $sql = "
        UPDATE categories
        SET
            name = '$name',
            icon = '$icon'
        WHERE id = '$id'
    ";

    mysqli_query($conn, $sql);

    $_SESSION['success_message'] = "Cập nhật danh mục thành công!";

    header("Location: index.php");
    exit;
} 
?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Category</title>

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
                    Edit Category
                </h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>Category Name</label>

                        <input
                            type="text"
                            name="name"
                            value="<?= $category['name'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Icon</label>

                        <input
                            type="text"
                            name="icon"
                            value="<?= $category['icon'] ?>"
                            class="form-control">

                    </div>

                    <button
                        type="submit"
                        name="update_category"
                        class="btn-product-save">

                        Update Category

                    </button>

                </form>

            </div>

            <?php include("../includes/footer.php"); ?>

        </div>

    </div>
    </div>

</body>
</html>