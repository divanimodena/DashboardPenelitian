<?php
session_start();
include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Pastikan dari form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data form
    $nama     = $_POST['nama'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Rapikan input
    $nama     = mysqli_real_escape_string($conn, $nama);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Validasi
    if (empty($nama) || empty($username) || empty($password)) {
        header("Location: pengguna.php?error=Data wajib diisi");
        exit;
    }

    // Cek username sudah ada atau belum
    $cekUser = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cekUser) > 0) {
        header("Location: pengguna.php?error=Username sudah terdaftar");
        exit;
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke tabel users
    $queryUser = mysqli_query($conn, "INSERT INTO users (nama, username, password)
                                      VALUES ('$nama', '$username', '$passwordHash')");

    if ($queryUser) {

        // Simpan log aktivitas
        $adminLogin = $_SESSION['username'] ?? 'Admin';
        $judul_aktivitas = "Pengguna";
        $deskripsi = "$adminLogin menambahkan pengguna baru: $username";
        $ikon = "👤";
        $waktu = date('Y-m-d H:i:s');

        mysqli_query($conn, "INSERT INTO aktivitas (judul, deskripsi, ikon, waktu)
                             VALUES ('$judul_aktivitas', '$deskripsi', '$ikon', '$waktu')");

        header("Location: pengguna.php?success=1");
        exit;

    } else {
        header("Location: pengguna.php?error=Gagal menambahkan pengguna");
        exit;
    }

} else {
    header("Location: pengguna.php");
    exit;
}
?>