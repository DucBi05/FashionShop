<?php

include("../includes/auth.php");
include("../../config/config.php");

$sql = "SELECT * FROM users WHERE role = 'customer'";

$keyword = trim($_GET['keyword'] ?? '');

if($keyword != '')
{
    $sql .= "
    AND users.fullname LIKE '%$keyword%'
    ";
}

$result = mysqli_query($conn, $sql);

$total_users = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM users WHERE role = 'customer'")
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

                <h2>Users</h2>

                <div class="top-bar">

                    <form method="GET" class="search-form">

                        <input
                            type="text"
                            name="keyword"
                            placeholder="Search for users..."
                            value="<?= $_GET['keyword'] ?? '' ?>">

                        <button type="submit">
                            🔍
                        </button>

                    </form>

                    <a href="add.php" class="btn-dashboard-add">
                        + Add User
                    </a>

                </div>

            </div>

            <div class="product-stats">

                <div class="stat-card">
                    <p>Total users</p>
                    <h3><?= $total_users ?></h3>
                </div>

            </div>

            <div class="table-responsive">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($user = mysqli_fetch_assoc($result)) { ?>

                        <tr>

                            <td>
                                <?= $user['id'] ?>
                            </td>

                            <td style="text-align: center;">

                                <img
                                    src="<?= $user['avatar'] ? "../../assets/uploads/users/". $user['avatar'] : "../../assets/uploads/users/default.jpg"  ?>"
                                    class="user-image">

                            </td>

                            <td>
                                <?= $user['fullname'] ?>
                            </td>

                            <td>
                                <?= $user['email'] ?>
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="edit.php?id=<?= $user['id'] ?>"
                                        class="btn-action btn-edit">

                                        Edit

                                    </a>

                                    <button
                                        type="button"
                                        class="btn-action btn-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="<?= $user['id'] ?>"
                                        data-name="<?= $user['fullname'] ?>">

                                        Delete

                                    </button>
                                </div>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">Xác nhận xóa</h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body">

                            Bạn có chắc muốn xóa người dùng "<span id="deleteProductName"></span>" không?

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

    const userId = button.getAttribute('data-id');
    const userName = button.getAttribute('data-name');

    document.getElementById('deleteProductName').textContent = userName;

    document.getElementById('confirmDeleteBtn').href = "delete.php?id=" + userId;

});

</script>

</html>