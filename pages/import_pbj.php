<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Data PBJ</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <style>
        body { background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .import-card { background: white; padding: 40px; border-radius: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.1); width: 100%; max-width: 650px; }
        .info-box { background: #f0fdf4; border-left: 5px solid #22c55e; padding: 20px; margin-bottom: 25px; border-radius: 12px; }
        .info-box p { margin: 0 0 10px 0; font-size: 14px; color: #166534; line-height: 1.6; }
        .info-box code { background: #dcfce7; padding: 2px 6px; border-radius: 4px; font-weight: bold; color: #14532d; }
        .file-input-wrapper { border: 2px dashed #e5e7eb; padding: 25px; border-radius: 16px; text-align: center; margin-bottom: 25px; }
        .btn-group { display: flex; gap: 15px; }
        .btn-main { flex: 1; padding: 14px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; text-decoration: none; text-align: center; font-size: 15px; }
        .btn-save { background: #24552f; color: white; }
        .btn-back { background: #6b7280; color: white; }
    </style>
</head>
<body>

<div class="import-card">
    <h2 style="color: #24552f; margin-top: 0;">Import Data Penelitian</h2>
    
    <div class="info-box">
        <p><strong>Panduan Format File:</strong></p>
        <p>Dukung file: <b>.xlsx / .xls (Excel)</b> atau <b>.csv</b>.</p>
        <p>Pastikan urutan kolom di file Anda adalah:</p>
        <code>Kode Riset | Kelti | TRL | Judul Penelitian | Nilai Anggaran | Sumber Dana | Bulan | Tahun</code>
    </div>

    <form action="proses_import.php" method="POST" enctype="multipart/form-data">
        <div class="file-input-wrapper">
            <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">Pilih File Excel Anda</label>
            <input type="file" name="file_import" accept=".xlsx, .xls, .csv" required>
        </div>
        
        <div class="btn-group">
            <button type="submit" name="import" class="btn-main btn-save">Mulai Import Data</button>
            <a href="data_pbj.php" class="btn-main btn-back">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>