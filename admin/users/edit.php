<?php

include("../../config/config.php");

$id = $_GET['id'];

$user_sql = "
    SELECT *
    FROM users
    WHERE id = '$id'
";

$user_result = mysqli_query($conn, $user_sql);

$user = mysqli_fetch_assoc($user_result);

if(isset($_POST['update_user'])) {

    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $address     = $_POST['address'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $password = $user['password'];
    }

    $image = $user['avatar'];

    if($_FILES['image']['name']) {

        $image = $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../assets/uploads/users/" . $image
        );
    }

    $sql = "
        UPDATE users

        SET
            fullname = '$name',
            email = '$email',
            password = '$password',
            phone = '$phone',
            address = '$address',
            avatar = '$image'
        WHERE id = '$id'
    ";

    mysqli_query($conn, $sql);

    $_SESSION['success_message'] = "Cập nhật người dùng thành công!";

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
                    Edit User
                </h2>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">

                        <label>User Name</label>

                        <input
                            type="text"
                            name="name"
                            value="<?= $user['fullname'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            value="<?= $user['email'] ?>"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Password</label>

                        <input
                            placeholder="*Để trống nếu không muốn đổi mật khẩu*" style="font-style: italic;"
                            type="password"
                            name="password"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Phone</label>

                        <input
                            type="text"
                            name="phone"
                            <?= $user['phone'] ? 'value="' . ($user['phone']) . '"' : 'placeholder="*Chưa có thông tin*" style="font-style: italic;"' ?>
                            class="form-control">

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

                            Avatar

                        </label>

                        <div class="upload-area" id="uploadArea">

                            <input
                                type="file"
                                id="imageInput"
                                name="image"
                                accept="image/*"
                                hidden>

                            <img
                                src="<?= $user['avatar'] ? "../../assets/uploads/users/" . $user['avatar'] : "../../assets/uploads/users/default.jpg" ?>"
                                id="imagePreview"
                                class="preview-image">

                            <div class="upload-content">

                                <h5>
                                    Change Avatar
                                </h5>

                                <p>
                                    Drag & Drop image here or click to browse
                                </p>

                                <small id="fileInfo">

                                    Current avatar:
                                    <?= htmlspecialchars($user['avatar']) ?>

                                </small>

                            </div>

                        </div>

                    </div>

                    <button
                        type="submit"
                        name="update_user"
                        class="btn-product-save">

                        Update User

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

        const currentValue = '<?= addslashes($user['address'] ?? '') ?>';
        if (currentValue) {
            const existingOption = Array.from(select.options).some(option => option.value === currentValue);
            if (!existingOption) {
                select.add(new Option(currentValue, currentValue));
            }
            select.value = currentValue;
        }
    } catch (error) {
        select.innerHTML = '<option value="">Không tải được dữ liệu</option>';
    }
}

window.addEventListener('DOMContentLoaded', loadProvinces);
</script>

</body>
</html>