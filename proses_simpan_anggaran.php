<?php
session_start();
include 'config/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Pastikan request dari form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data dari form
    $nama_kegiatan = $_POST['nama_kegiatan'] ?? '';
    $jumlah        = $_POST['jumlah'] ?? '';
    $tahun         = $_POST['tahun'] ?? '';
    $keterangan    = $_POST['keterangan'] ?? '';

    // Bersihkan input
    $nama_kegiatan = mysqli_real_escape_string($conn, $nama_kegiatan);
    $jumlah        = mysqli_real_escape_string($conn, $jumlah);
    $tahun         = mysqli_real_escape_string($conn, $tahun);
    $keterangan    = mysqli_real_escape_string($conn, $keterangan);

    // Validasi sederhana
    if (empty($nama_kegiatan) || empty($jumlah) || empty($tahun)) {
        header("Location: anggaran.php?error=Data wajib diisi");
        exit;
    }

    // Simpan ke tabel anggaran
    $queryAnggaran = mysqli_query($conn, "INSERT INTO anggaran (nama_kegiatan, jumlah, tahun, keterangan) 
                                          VALUES ('$nama_kegiatan', '$jumlah', '$tahun', '$keterangan')");

    if ($queryAnggaran) {

        // Ambil nama user yang login
        $username = $_SESSION['username'] ?? 'Admin';

        // Siapkan data aktivitas
        $judul_aktivitas = "Anggaran";
        $deskripsi = "$username menambahkan anggaran: $nama_kegiatan";
        $waktu = date('Y-m-d H:i:s');
        $ikon = "💰";

        // Simpan ke tabel aktivitas
        mysqli_query($conn, "INSERT INTO aktivitas (judul, deskripsi, ikon, waktu) 
                             VALUES ('$judul_aktivitas', '$deskripsi', '$ikon', '$waktu')");

        header("Location: anggaran.php?success=1");
        exit;

    } else {
        header("Location: anggaran.php?error=Gagal menyimpan data");
        exit;
    }

} else {
    header("Location: anggaran.php");
    exit;
}
?>