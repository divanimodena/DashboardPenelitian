<?php
include 'config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("ID tidak valid");
}

$result = mysqli_query($conn, "SELECT * FROM pbj WHERE id = $id");
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data tidak ditemukan");
}

if (isset($_POST['update_pbj'])) {
    $kelti = mysqli_real_escape_string($conn, trim($_POST['kelti']));
    $trl = (int) $_POST['trl'];
    $kode_riset = mysqli_real_escape_string($conn, trim($_POST['kode_riset']));
    $nilai_anggaran = (int) $_POST['nilai_anggaran'];
    $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan']));
    $tahun = (int) $_POST['tahun'];

    $query = "UPDATE pbj SET
                kelti = '$kelti',
                trl = '$trl',
                kode_riset = '$kode_riset',
                nilai_anggaran = '$nilai_anggaran',
                bulan = '$bulan',
                tahun = '$tahun'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: data_pbj.php?pesan=update_sukses");
        exit;
    } else {
        header("Location: data_pbj.php?pesan=update_gagal");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data PBJ</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
</head>
<body>

<div class="main-content">
    <div class="form-card">
        <h3>Edit Data PBJ</h3>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Kelti</label>
                    <input type="text" name="kelti" required value="<?= htmlspecialchars($data['kelti']); ?>">
                </div>

                <div class="form-group">
                    <label>TRL</label>
                    <input type="number" name="trl" required value="<?= htmlspecialchars($data['trl']); ?>">
                </div>

                <div class="form-group">
                    <label>Kode Riset</label>
                    <input type="text" name="kode_riset" required value="<?= htmlspecialchars($data['kode_riset']); ?>">
                </div>

                <div class="form-group">
                    <label>Nilai Anggaran</label>
                    <input type="number" name="nilai_anggaran" required value="<?= htmlspecialchars($data['nilai_anggaran']); ?>">
                </div>

                <div class="form-group">
                    <label>Bulan</label>
                    <input type="text" name="bulan" value="<?= htmlspecialchars($data['bulan']); ?>">
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" required value="<?= htmlspecialchars($data['tahun']); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_pbj" class="save-btn">Update Data</button>
                <a href="data_pbj.php" class="cancel-btn">Kembali</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>