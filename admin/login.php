<?php

include("../config/config.php");

$error = "";

if(isset($_POST['login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "
        SELECT *
        FROM users
        WHERE email = '$email'
        AND role = 'admin'
        LIMIT 1
    ";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password,$user['password']))
        {
            $_SESSION['admin_id'] = $user['id'];

            $_SESSION['admin_name'] = $user['fullname'];

            header("Location: dashboard.php");
            exit;
        }

        $error = "Sai mật khẩu";
    }
    else
    {
        $error = "Tài khoản không tồn tại";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Admin Login</title>

    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    rel="stylesheet">

    <link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

<div class="login-container">

    <!-- LEFT -->
    <div class="login-left">

        <div class="overlay">

            <h1>StyleVibe</h1>

            <p>
                Fashion Store Administration
            </p>

        </div>

    </div>

    <!-- RIGHT -->
    <div class="login-right">

        <div class="login-box">

            <h2>Welcome Back</h2>

            <p class="subtitle">

                Sign in to continue

            </p>

            <?php if($error): ?>

                <div class="error">

                    <?= $error ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="input-group">

                    <i class="fas fa-envelope"></i>

                    <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required>

                </div>

                <div class="input-group">

                    <i class="fas fa-lock"></i>

                    <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required>

                </div>

                <button
                type="submit"
                name="login">

                    Login

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>