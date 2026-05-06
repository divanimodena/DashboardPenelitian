<?php
    session_start();
    include 'config/koneksi.php';

    if ($_SESSION['role'] !== 'admin') {
        exit;
    }

    $id = $_GET['id'];

    mysqli_query($conn, "UPDATE users SET status='nonaktif' WHERE id=$id");

    header("Location: admin_users.php");