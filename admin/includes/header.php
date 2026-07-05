<?php

$noti_sql = "
    SELECT COUNT(*) AS total
    FROM notifications
    WHERE is_read = 0
";

$noti_result = mysqli_query($conn,$noti_sql);

$noti_count = mysqli_fetch_assoc($noti_result)['total'];

?>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<div class="topbar">

    <div class="topbar-left">

        <button id="toggleSidebar" class="menu-btn">

            <i class="fas fa-bars"></i>

        </button>

    </div>

    <div class="topbar-right">

        <button class="icon-btn notification-btn">

            <i class="fas fa-bell"></i>

            <?php if($noti_count > 0): ?>

                <span class="notification-dot"></span>

            <?php endif; ?>

        </button>

        <div class="notification-dropdown">

        <?php

        $notifications = mysqli_query(
            $conn,
            "
            SELECT *
            FROM notifications
            WHERE is_read = 0
            ORDER BY id DESC
            LIMIT 10
            "
        );
        ?>

        <?php if(mysqli_num_rows($notifications) === 0): ?>

            <div class="notification-item">
                No notifications.
            </div>

        <?php else: ?>

            <?php while($row = mysqli_fetch_assoc($notifications)): ?>

                <div class="notification-item">
                    <?= htmlspecialchars($row['message']) ?>
                    <small>
                        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                    </small>
                </div>

            <?php endwhile; ?>

        <?php endif; ?>
        </div>

        <div class="admin-profile">

            <span>
                <?= $_SESSION['admin_name'] ?>
            </span>

        </div>

    </div>

</div>

<script>

const bellBtn =
document.querySelector(".notification-btn");

const dropdown =
document.querySelector(".notification-dropdown");

bellBtn.addEventListener("click", () => {

    dropdown.style.display =
        dropdown.style.display === "block"
            ? "none"
            : "block";

    fetch("mark_notifications_read.php");

});

</script>