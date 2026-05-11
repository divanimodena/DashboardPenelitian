<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$pesan = "";

// 1. LOGIKA TAMBAH DATA
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = 'active';

    $cek = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='alert alert-danger'>Gagal! Username sudah terdaftar.</div>";
    } else {
        $query = "INSERT INTO users (nama, username, password, role, status) VALUES ('$nama', '$username', '$password', '$role', '$status')";
        if (mysqli_query($conn, $query)) {
            $pesan = "<div class='alert alert-success'>Berhasil! Data user ditambahkan.</div>";
        }
    }
}

// 2. LOGIKA HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    echo "<script>alert('Data berhasil dihapus!'); window.location='pengguna.php';</script>";
    exit;
}

$queryUsers = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-header h1 { margin: 0; color: #1f4d2e; font-size: 28px; }
        
        .card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #edf1ed; }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #444; }
        .form-input { width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 8px; outline: none; }
        
        .btn-simpan { background: #1e88e5; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-hapus { background: #e53935; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 13px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #a53258; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; color: #333; }
        
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .alert-danger { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-logo">
                <h2>Dashboard</h2>
                <span>Manajemen</span>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item"><i class="fa-solid fa-house"></i><span>Home</span></a>
                <a href="summary.php" class="menu-item"><i class="fa-solid fa-chart-column"></i><span>Summary</span></a>
                <a href="data_pbj.php" class="menu-item"><i class="fa-solid fa-folder-open"></i><span>Data PBJ</span></a>
                <a href="pengguna.php" class="menu-item active"><i class="fa-solid fa-users"></i><span>Pengguna</span></a>
            </nav>
        </div>
        <div class="sidebar-bottom">
            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <h1>Manajemen User</h1>
        </div>

        <?= $pesan; ?>

        <div class="card">
            <h3 style="margin-top: 0;">+ Tambah Data User</h3>
            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-input" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="simpan" class="btn-simpan"><i class="fa-solid fa-save"></i> Simpan User</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Daftar Pengguna Sistem</h3>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($queryUsers) > 0) : ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($queryUsers)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['role']); ?></td>
                                    <td><?= htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <a href="pengguna.php?hapus=<?= $row['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus akun ini?');">
                                           <i class="fa-solid fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="text-align: center;">Belum ada data user.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>