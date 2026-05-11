<?php
session_start();
include '../config/koneksi.php'; 

// ========================================================================
// 1. OTORISASI & KEAMANAN
// ========================================================================
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

// Gembok khusus Admin (Sesuaikan 'admin' dengan yang ada di database)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) != 'admin') {
    echo "<script>
            alert('⛔ AKSES DITOLAK! Hanya Admin yang berhak mengedit data.');
            window.location.href = 'data_pbj.php';
          </script>";
    exit;
}

// ========================================================================
// 2. AMBIL DATA DARI DATABASE (BERDASARKAN ID ATAU KODE RISET)
// ========================================================================
$data = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM pbj WHERE id = $id");
    if ($result) $data = mysqli_fetch_assoc($result);

} elseif (isset($_GET['kode'])) {
    $kode = mysqli_real_escape_string($conn, trim($_GET['kode']));
    $result = mysqli_query($conn, "SELECT * FROM pbj WHERE kode_riset = '$kode'");
    if ($result) $data = mysqli_fetch_assoc($result);
}

// Hentikan proses jika data benar-benar tidak ditemukan
if (!$data) {
    echo "<script>
            alert('Data tidak ditemukan di database.');
            window.location.href = 'data_pbj.php';
          </script>";
    exit;
}

// ========================================================================
// 3. PROSES SIMPAN PERUBAHAN (UPDATE)
// ========================================================================
if (isset($_POST['update_pbj'])) {
    // Bersihkan data dari karakter berbahaya (Anti SQL-Injection)
    $kelti            = mysqli_real_escape_string($conn, trim($_POST['kelti']));
    $trl              = (int) $_POST['trl'];
    $kode_riset_baru  = mysqli_real_escape_string($conn, trim($_POST['kode_riset'])); 
    $judul_penelitian = mysqli_real_escape_string($conn, trim($_POST['judul_penelitian'])); 
    $nilai_anggaran   = (int) $_POST['nilai_anggaran'];
    $bulan            = mysqli_real_escape_string($conn, trim($_POST['bulan']));
    $tahun            = (int) $_POST['tahun'];
    $id_target        = (int) $data['id']; // Gunakan ID asli sebagai patokan update

    $query = "UPDATE pbj SET
                kelti            = '$kelti',
                trl              = '$trl',
                kode_riset       = '$kode_riset_baru',
                judul_penelitian = '$judul_penelitian',
                nilai_anggaran   = '$nilai_anggaran',
                bulan            = '$bulan',
                tahun            = '$tahun'
              WHERE id = $id_target";

    if (mysqli_query($conn, $query)) {
        header("Location: data_pbj.php?msg=update_sukses");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan perubahan: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data PBJ</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <style>
        body { background-color: #f4f7f6; display: flex; justify-content: center; padding: 40px; }
        .main-content { width: 100%; max-width: 800px; margin: 0; padding: 0; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group.full-width { grid-column: 1 / -1; } 
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit; }
        .form-actions { display: flex; gap: 10px; margin-top: 24px; }
        .save-btn { padding: 10px 20px; background-color: #24552f; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .cancel-btn { padding: 10px 20px; background-color: #ccc; color: #333; text-decoration: none; border-radius: 6px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="form-card">
        <h3 style="color: #24552f; margin-bottom: 20px;">Edit Data PBJ</h3>

        <form method="POST">
            <div class="form-grid">
                
                <div class="form-group">
                    <label>Kelti</label>
                    <input type="text" name="kelti" required value="<?= htmlspecialchars($data['kelti'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>TRL</label>
                    <input type="number" name="trl" required value="<?= htmlspecialchars($data['trl'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Kode Riset</label>
                    <input type="text" name="kode_riset" required value="<?= htmlspecialchars($data['kode_riset'] ?? ''); ?>">
                </div>

                <div class="form-group full-width">
                    <label>Judul Penelitian</label>
                    <input type="text" name="judul_penelitian" required value="<?= htmlspecialchars($data['judul_penelitian'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Nilai Anggaran (Rp)</label>
                    <input type="number" name="nilai_anggaran" required value="<?= htmlspecialchars($data['nilai_anggaran'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Bulan</label>
                    <select name="bulan" required>
                        <option value="">-- Pilih Bulan --</option>
                        <?php 
                        // Array daftar bulan untuk memudahkan pembuatan dropdown
                        $daftar_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $bulan_tersimpan = htmlspecialchars($data['bulan'] ?? '');

                        foreach ($daftar_bulan as $bln) {
                            // Tandai bulan yang sedang terpilih di database
                            $selected = ($bulan_tersimpan === $bln) ? 'selected' : '';
                            echo "<option value='$bln' $selected>$bln</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" required value="<?= htmlspecialchars($data['tahun'] ?? ''); ?>">
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" name="update_pbj" class="save-btn">Simpan Perubahan</button>
                <a href="data_pbj.php" class="cancel-btn">Batal / Kembali</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>