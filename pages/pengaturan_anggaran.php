<?php
session_start();
include '../config/koneksi.php';
$currentPage = 'pengaturan_anggaran.php';

if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

$pesan = "";

// Logika Simpan Perubahan (Satu tombol untuk simpan semua)
if (isset($_POST['simpan_pengaturan'])) {
    // 1. Simpan Total Anggaran Keseluruhan
    $total_induk = str_replace(['.', ','], '', $_POST['total_induk']);
    mysqli_query($conn, "UPDATE pengaturan_global SET nilai = '$total_induk' WHERE nama_pengaturan = 'total_anggaran_induk'");

    // 2. Simpan Anggaran ke-7 Kelti
    foreach ($_POST['pagu_kelti'] as $id_kelti => $nilai_kelti) {
        $nilai_kelti = str_replace(['.', ','], '', $nilai_kelti);
        mysqli_query($conn, "UPDATE master_kelti SET pagu_anggaran = '$nilai_kelti' WHERE id = '$id_kelti'");
    }

    $pesan = "<div style='padding:15px; background:#e6f4ea; color:#16a34a; border-radius:10px; margin-bottom:20px; border-left: 5px solid #16a34a;'><b>Sukses!</b> Total Anggaran Keseluruhan dan Pagu Kelti berhasil diperbarui.</div>";
}

// Ambil data dari database untuk ditampilkan di Form
$qGlobal = mysqli_query($conn, "SELECT nilai FROM pengaturan_global WHERE nama_pengaturan = 'total_anggaran_induk'");
$dGlobal = mysqli_fetch_assoc($qGlobal);
$totalAnggaranInduk = $dGlobal['nilai'] ?? 0;

$queryKelti = mysqli_query($conn, "SELECT * FROM master_kelti");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Anggaran - PPKS</title>
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .settings-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 25px;}
        .input-pagu { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 16px; transition: 0.3s; }
        .input-pagu:focus { border-color: #16a34a; outline: none; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1); }
        .btn-save { background: #16a34a; color: white; border: none; padding: 15px 30px; border-radius: 12px; cursor: pointer; font-weight: bold; font-size: 16px; width: 100%; transition: 0.3s; }
        .btn-save:hover { background: #15803d; transform: translateY(-2px); }
        .section-title { font-size: 18px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <h1 style="margin-bottom: 5px;">Pengaturan Anggaran</h1>
        <p style="color:#64748b; margin-bottom:30px;">Kelola Total Anggaran Induk PPKS dan alokasi dana untuk masing-masing Kelti.</p>
        
        <?= $pesan; ?>

        <form action="" method="POST">
            <div class="settings-card" style="border-left: 5px solid #2196f3;">
                <h2 class="section-title"><i class="fa-solid fa-wallet" style="color: #2196f3;"></i> Total Anggaran Keseluruhan</h2>
                <label style="display:block; margin-bottom: 10px; color:#64748b;">Masukkan total anggaran institusi (Rp):</label>
                <input type="number" name="total_induk" value="<?= $totalAnggaranInduk; ?>" class="input-pagu" style="border-color: #2196f3; font-weight: bold; font-size: 18px;" placeholder="Contoh: 5000000000" required>
            </div>

            <div class="settings-card" style="border-left: 5px solid #f59e0b;">
                <h2 class="section-title"><i class="fa-solid fa-chart-pie" style="color: #f59e0b;"></i> Alokasi Anggaran per Kelti</h2>
                <table style="width:100%; border-collapse: separate; border-spacing: 0 15px;">
                    <thead>
                        <tr style="text-align: left; color:#94a3b8; font-size: 13px;">
                            <th>NAMA KELOMPOK PENELITI (KELTI)</th>
                            <th width="40%">ALOKASI ANGGARAN (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($queryKelti)): ?>
                        <tr>
                            <td style="font-weight: 600; color:#334155;"><?= $row['nama_kelti']; ?></td>
                            <td>
                                <input type="number" name="pagu_kelti[<?= $row['id']; ?>]" value="<?= $row['pagu_anggaran']; ?>" class="input-pagu" placeholder="0">
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <button type="submit" name="simpan_pengaturan" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan Anggaran
            </button>
        </form>
    </main>
</div>
</body>
</html>