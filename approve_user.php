<?php
    session_start();
    include 'koneksi.php'; // sesuaikan kalau pakai config

    // cek login
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }

    // cek role admin
    if ($_SESSION['role'] !== 'admin') {
        echo "Akses ditolak";
        exit;
    }

    // ambil id user
    $id = $_GET['id'];

    // update status jadi aktif
    mysqli_query($conn, "UPDATE users SET status='aktif' WHERE id=$id");

    header("Location: admin_users.php");
    exit;
    ?>