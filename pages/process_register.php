<?php
include 'config/koneksi.php';

$nama = $_POST['nama'] ?? '';
$username = $_POST['username'] ?? '';
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);


$nama = mysqli_real_escape_string($conn, $nama);
$username = mysqli_real_escape_string($conn, $username);
$password = mysqli_real_escape_string($conn, $password);

$cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

if (mysqli_num_rows($cek) > 0) {
    header("Location: register.php?error=1");
    exit;
}

$query = "INSERT INTO users (nama, username, password, role, status)
          VALUES ('$nama', '$username', '$password', 'peneliti', 'pending')";

if (mysqli_query($conn, $query)) {
    header("Location: register.php?success=1");
    exit;
} else {
    echo "Gagal daftar: " . mysqli_error($conn);
}
?>