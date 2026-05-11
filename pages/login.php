<?php
session_start(); // Pastikan ini ada di baris paling atas
include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Cari user di database
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    // 2. Cek apakah user ditemukan
    if (mysqli_num_rows($result) === 1) {
        $data_user = mysqli_fetch_assoc($result);

        // 3. Cek apakah passwordnya cocok
        // (Gunakan password_verify jika dipassword di-hash, atau cek string biasa)
        if ($password === $data_user['password']) {
            
            // --- DISINI TEMPATNYA ---
            $_SESSION['login']    = true;
            $_SESSION['username'] = $data_user['username'];
            $_SESSION['role']     = $data_user['role']; // Baris kunci untuk admin/user
            // ------------------------

            header("Location: home.php"); // Lempar ke dashboard
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard</title>
    <link rel="stylesheet" href=" ../Assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-page">

<div class="login-wrapper">
    <div class="login-card">

        <div class="left-panel">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="right-panel">
            <div class="login-box">

                <div class="login-header">
                    <div class="icon-circle">
    <img src="../Assets/image/logo_ppks.jpeg" alt="Logo PPKS" style="width: 100%; height: auto;">
</div>
                    <h2>LOGIN</h2>
                </div>

                <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                    <div class="alert-error">Username atau password salah.</div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] == 2): ?>
                    <div class="alert-error">Akun Anda belum aktif atau belum disetujui admin.</div>
                <?php endif; ?>

                <form action="process_login.php" method="POST" autocomplete="off">
                    <input type="password" name="fakepasswordremembered" style="display:none">

                    <div class="input-group">
                        <span><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>

                    <div class="input-group">
                        <span><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn">LOGIN</button>
                </form>

                <div class="social-login">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>