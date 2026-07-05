<?php

include("../../config/config.php");

if(isset($_POST['add_category'])) {

    $name = $_POST['name'];
    $icon = $_POST['icon'];

    $sql = "
        INSERT INTO categories (name, icon)
        VALUES ('$name', '$icon')
    ";

    mysqli_query($conn, $sql);

    $_SESSION['success_message'] = "Thêm danh mục thành công!";

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Category</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../assets/css/admin.css">

</head>

<body>

<div class="admin-layout">

    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">

        <?php include("../includes/header.php"); ?>

        <div class="table-card">

            <div class="table-border">

                <h2 class="mb-4">
                    Add Category
                </h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>Category Name</label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label>Icon</label>

                        <input
                            type="text"
                            name="icon"
                            class="form-control"
                            required>

                    </div>

                    <button
                        type="submit"
                        name="add_category"
                        class="btn-product-save">

                        Add Category

                    </button>

                </form>

            </div>

        </div>

        <?php include("../includes/footer.php"); ?>
        
    </div>
    </div>

</body>
</html>