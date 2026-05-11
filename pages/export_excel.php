<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// Set zona waktu
date_default_timezone_set('Asia/Jakarta');
$tanggal_export = date('d-F-Y_H-i');

// Perintah ajaib ini akan memaksa browser mengunduh halaman ini sebagai file Excel (.xls)
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Manajemen_PBJ_$tanggal_export.xls");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        /* CSS ini akan dibaca oleh Excel untuk merapikan tabel */
        table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #000000; padding: 8px; text-align: left; }
        th { background-color: #2f7d32; color: #ffffff; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .judul { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .subjudul { font-size: 14px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="judul">LAPORAN REKAPITULASI ANGGARAN PENELITIAN (PBJ)</div>
    <div class="subjudul">Pusat Penelitian Kelapa Sawit<br>Tanggal Tarik Data: <?= date('d/m/Y H:i:s') ?></div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Riset</th>
                <th>Kelompok Peneliti (Kelti)</th>
                <th>TRL</th>
                <th>Judul Penelitian</th>
                <th>Sumber Dana</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Nilai Anggaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil data dari database, urutkan berdasarkan Kelti lalu Tahun terbaru
            $query = mysqli_query($conn, "SELECT * FROM pbj ORDER BY kelti ASC, tahun DESC, bulan ASC");
            $no = 1;
            $total_keseluruhan = 0;

            while($row = mysqli_fetch_assoc($query)){
                // Tangani nilai anggaran agar tidak error jika kosong
                $anggaran = isset($row['nilai_anggaran']) ? $row['nilai_anggaran'] : (isset($row['anggaran']) ? $row['anggaran'] : 0);
                $total_keseluruhan += $anggaran;
                
                // Set default untuk kolom baru jika ada data lama yang kosong
                $sumber_dana = isset($row['sumber_dana']) ? $row['sumber_dana'] : 'Internal';
                $bulan = isset($row['bulan']) ? $row['bulan'] : '-';
                $judul = isset($row['judul_penelitian']) ? $row['judul_penelitian'] : '-';
            ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= $row['kode_riset']; ?></td>
                <td><?= $row['kelti']; ?></td>
                <td class="text-center"><?= $row['trl']; ?></td>
                <td><?= $judul; ?></td>
                <td class="text-center"><?= $sumber_dana; ?></td>
                <td class="text-center"><?= $bulan; ?></td>
                <td class="text-center"><?= $row['tahun']; ?></td>
                <td class="text-right"><?= $anggaran; ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right" style="background-color: #d1fae5; color: #065f46;">TOTAL ANGGARAN KESELURUHAN</th>
                <th class="text-right" style="background-color: #d1fae5; color: #065f46; font-weight: bold;">
                    <?= $total_keseluruhan; ?>
                </th>
            </tr>
        </tfoot>
    </table>

</body>
</html>