<?php
include 'config/koneksi.php';

// Mengatur Header agar browser mengenali ini sebagai file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Realisasi_Anggaran.xls");

// Ambil data dari database (Mekanisme Debet: menampilkan semua data PBJ)
$query = mysqli_query($conn, "SELECT * FROM pbj ORDER BY id DESC");
?>

<h2>LAPORAN REALISASI ANGGARAN PENELITIAN</h2>
<p>Dihasilkan secara otomatis dari sistem pada: <?= date('d-m-Y H:i'); ?></p>

<table border="1">
    <thead>
        <tr style="background-color: #2f7d32; color: white;">
            <th>No</th>
            <th>Kelti</th>
            <th>Judul Penelitian</th>
            <th>Nilai Anggaran</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $totalSemua = 0;
        while($row = mysqli_fetch_assoc($query)): 
            $totalSemua += $row['nilai_anggaran'];
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['kelti']; ?></td>
            <td><?= $row['judul']; ?></td>
            <td><?= number_format($row['nilai_anggaran'], 0, ',', '.'); ?></td>
            <td><?= ucfirst($row['bulan']); ?></td>
            <td><?= $row['tahun']; ?></td>
            <td><?= ucfirst($row['status']); ?></td>
        </tr>
        <?php endwhile; ?>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="3" align="right">TOTAL REALISASI:</td>
            <td colspan="4">Rp <?= number_format($totalSemua, 0, ',', '.'); ?></td>
        </tr>
    </tbody>
</table>