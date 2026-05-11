<?php
session_start();

// ========================================================================
// 1. OTORISASI & KEAMANAN
// ========================================================================
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// Gembok khusus Admin (Tolak akses jika yang login bukan admin)
// Catatan: Jika di database kamu role-nya "Administrator", ganti kata 'admin' di bawah ini
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'admin') {
    echo "<script>
            alert('⛔ AKSES DITOLAK! Hanya Admin yang berhak menambah data anggaran.');
            window.location.href = 'data_pbj.php';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data PBJ Baru</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <style>
        body { background-color: #f4f7f6; display: flex; justify-content: center; padding: 40px; }
        .main-content { width: 100%; max-width: 800px; margin: 0; padding: 0; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group select, .form-group input { 
            padding: 10px; border: 1px solid #ccc; border-radius: 6px; 
            outline: none; background: #fff; font-size: 14px; font-family: inherit;
        }
        .form-actions { display: flex; gap: 10px; margin-top: 24px; }
        .save-btn { padding: 10px 20px; background-color: #24552f; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .cancel-btn { padding: 10px 20px; background-color: #ccc; color: #333; text-decoration: none; border-radius: 6px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="form-card">
        <h3 style="color: #24552f; margin-bottom: 20px;">Tambah Data Penelitian (PBJ) Baru</h3>

        <form action="proses_simpan_anggaran.php" method="POST">
            <div class="form-grid">
                
                <div class="form-group">
                    <label>Kelti</label>
                    <input type="text" name="kelti" placeholder="Contoh: Pemuliaan Tanaman" required>
                </div>

                <div class="form-group">
                    <label>TRL</label>
                    <input type="number" name="trl" placeholder="Contoh: 3" required>
                </div>

                <div class="form-group">
                    <label>Kode Riset</label>
                    <input type="text" name="kode_riset" placeholder="Contoh: 23.2.09.01" required>
                </div>

                <div class="form-group full-width">
                    <label>Judul Penelitian</label>
                    <input type="text" name="judul_penelitian" placeholder="Masukkan judul penelitian lengkap" required>
                </div>

                <div class="form-group">
                    <label>Nilai Anggaran (Rp)</label>
                    <input type="number" name="nilai_anggaran" placeholder="Contoh: 150000000" required>
                </div>

                <div class="form-group">
                    <label>Sumber Dana</label>
                    <select name="sumber_dana" required>
                        <option value="Internal">Internal</option>
                        <option value="Eksternal">Eksternal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Bulan</label>
                    <select name="bulan" required>
                        <option value="">-- Pilih Bulan --</option>
                        <?php 
                        // Menggunakan array agar penulisan kode lebih bersih dan singkat
                        $daftar_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        foreach ($daftar_bulan as $bln) {
                            echo "<option value='$bln'>$bln</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" value="<?= date('Y') ?>" required>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" name="simpan_data" class="save-btn">Simpan Data Baru</button>
                <a href="data_pbj.php" class="cancel-btn">Batal / Kembali</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>