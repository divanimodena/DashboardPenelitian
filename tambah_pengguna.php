<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "INSERT INTO users (username, password, status)
    VALUES ('$username', '$password', '$status')");

    header("Location: pengguna.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengguna</title>

    <link rel="stylesheet" href="Assets/css/dashboard_modern.css">

    <style>
        .form-container{
            max-width:700px;
            margin:50px auto;
            background:white;
            padding:40px;
            border-radius:24px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
        }

        .form-container h1{
            margin-bottom:10px;
            color:#1f5c2e;
        }

        .form-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-top:25px;
        }

        .form-group{
            display:flex;
            flex-direction:column;
        }

        .form-group label{
            margin-bottom:8px;
            font-weight:600;
        }

        .form-group input,
        .form-group select{
            padding:14px;
            border-radius:14px;
            border:1px solid #d7dfd7;
            outline:none;
        }

        .btn-area{
            margin-top:25px;
            display:flex;
            gap:14px;
        }

        .save-btn{
            background:#22c55e;
            color:white;
            border:none;
            padding:14px 24px;
            border-radius:14px;
            font-weight:700;
            cursor:pointer;
        }

        .back-btn{
            background:#6b7280;
            color:white;
            padding:14px 24px;
            border-radius:14px;
            text-decoration:none;
            font-weight:700;
        }
    </style>
</head>

<body>

<div class="form-container">

    <h1>Tambah Pengguna</h1>
    <p>Tambahkan user baru ke sistem dashboard.</p>

    <form method="POST">

        <div class="form-grid">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Status</label>

                <select name="status">
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

        </div>

        <div class="btn-area">
            <button type="submit" name="simpan" class="save-btn">
                Simpan User
            </button>

            <a href="pengguna.php" class="back-btn">
                Kembali
            </a>
        </div>

    </form>

</div>

</body>
</html>