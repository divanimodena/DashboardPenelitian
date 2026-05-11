<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Dashboard</title>
    <link rel="stylesheet" href="Assets/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="left-panel">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>

            <div class="left-content">
                <button class="tab-btn" onclick="window.location.href='login.php'">LOGIN</button>
                <button class="tab-btn active">REGISTER</button>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-header">
                <h2>REGISTER</h2>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert-success">
                    Pendaftaran berhasil. Tunggu persetujuan admin.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error">
                    Username sudah digunakan.
                </div>
            <?php endif; ?>

            <form action="process_register.php" method="POST" class="login-form">
                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <input type="username" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="btn-area">
                    <button type="submit" class="login-btn">DAFTAR</button>
                </div>
            </form>

            <div class="social-login">
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>

    </div>
</div>

</body>
</html>