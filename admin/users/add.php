<?php

include("../../config/config.php");

if(isset($_POST['add_user'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        
        $original_name = $_FILES['image']['name'];
        
        $image = time() . "_" . $original_name;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../assets/uploads/users/" . $image
        );
        
    } else {
        $image = "default.jpg";
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO users
        (
            fullname,
            email,
            password,
            phone,
            address,
            avatar
        )

        VALUES
        (
            '$name',
            '$email',
            '$password',
            '$phone',
            '$address',
            '$image'
        )
    ";

    mysqli_query($conn, $sql);

    $_SESSION['success_message'] = "Thêm người dùng thành công!";

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add User</title>

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
                    Add User
                </h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>User Name</label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label>Phone</label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label>Tỉnh / Thành phố</label>

                        <select
                            id="addressSelect"
                            name="address"
                            class="form-control"
                            required>
                            <option value="">Đang tải...</option>
                        </select>
                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Image
                        </label>

                        <div class="upload-area" id="uploadArea">

                            <input
                                type="file"
                                id="imageInput"
                                name="image"
                                accept="image/*"
                                hidden
                                required>

                            <img
                                src="https://via.placeholder.com/300x300?text=Preview"
                                id="imagePreview"
                                class="preview-image">

                            <div class="upload-content">

                                <h5>
                                    Drag & Drop Image Here
                                </h5>

                                <p>
                                    or click to browse
                                </p>

                                <small id="fileInfo">

                                    No file selected

                                </small>

                            </div>

                        </div>

                    </div>

                    <button
                        type="submit"
                        name="add_user"
                        class="btn-product-save">

                        Add User

                    </button>

                </form>

            </div>

        </div>

        <?php include("../includes/footer.php"); ?>
        
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

/* CLICK */

uploadArea.addEventListener("click", () => {

    imageInput.click();

});

/* FILE CHOOSE */

imageInput.addEventListener("change", () => {

    previewFile(
        imageInput.files[0]
    );

});

/* DRAG OVER */

uploadArea.addEventListener(
    "dragover",
    (e) => {

        e.preventDefault();

        uploadArea.classList.add(
            "dragover"
        );

    }
);

/* DRAG LEAVE */

uploadArea.addEventListener(
    "dragleave",
    () => {

        uploadArea.classList.remove(
            "dragover"
        );

    }
);

/* DROP */

uploadArea.addEventListener(
    "drop",
    (e) => {

        e.preventDefault();

        uploadArea.classList.remove(
            "dragover"
        );

        const file =
            e.dataTransfer.files[0];

        imageInput.files =
            e.dataTransfer.files;

        previewFile(file);

    }
);

/* PREVIEW */

function previewFile(file)
{
    if(!file) return;

    const reader =
        new FileReader();

    reader.onload = function(e)
    {
        imagePreview.src =
            e.target.result;
    }

    reader.readAsDataURL(file);

    const size =
        (file.size / 1024 / 1024)
        .toFixed(2);

    fileInfo.innerHTML =

        file.name +

        " (" +

        size +

        " MB)";
}

</script>

<script>
async function loadProvinces() {
    const select = document.getElementById('addressSelect');
    if (!select) return;

    try {
        const response = await fetch('https://provinces.open-api.vn/api/p/');
        const provinces = await response.json();
        select.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name;
            option.textContent = province.name;
            select.appendChild(option);
        });
    } catch (error) {
        select.innerHTML = '<option value="">Không tải được dữ liệu</option>';
    }
}

window.addEventListener('DOMContentLoaded', loadProvinces);
</script>

</body>
</html>