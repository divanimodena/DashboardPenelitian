<?php
    session_start();
    include('../config/koneksi.php');

    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }

    // Proteksi: Halaman ini beserta tombol tambah/edit/hapus di dalamnya hanya bisa diakses oleh admin
    if ($_SESSION['role'] !== 'admin') {
        echo "Akses ditolak";
        exit;
    }

    $query = "SELECT * FROM users ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: 1200px; /* Membatasi lebar agar tabel tidak melar tak terhingga di layar besar */
            margin: 40px auto;
        }
        .header-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 {
            margin: 0;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Efek bayangan halus */
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left; /* Teks diratakan ke kiri agar sejajar dan rapi */
        }
        th {
            background: #b03a72;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #fcfcfc; /* Efek hover saat baris disorot mouse */
        }
        .btn {
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 13px;
            display: inline-block;
            margin-right: 3px;
        }
        .btn-tambah {
            background: #007bff;
            font-size: 14px;
            padding: 10px 15px;
        }
        .btn-tambah:hover { background: #0056b3; }
        
        .approve { background: #28a745; }
        .approve:hover { background: #218838; }
        
        .nonaktif { background: #ffc107; color: #212529; }
        .nonaktif:hover { background: #e0a800; }
        
        .hapus { background: #dc3545; }
        .hapus:hover { background: #c82333; }

        /* Mengatur proporsi lebar setiap kolom */
        .col-no { width: 5%; }
        .col-nama { width: 25%; }
        .col-username { width: 25%; }
        .col-role { width: 10%; }
        .col-status { width: 10%; }
        .col-aksi { width: 25%; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-title">
        <h2>Manajemen User</h2>
        <!-- Tombol tambah data khusus admin -->
        <a href="tambah_user.php" class="btn btn-tambah">+ Tambah Data User</a>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nama">Nama</th>
                <th class="col-username">Username</th>
                <th class="col-role">Role</th>
                <th class="col-status">Status</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <!-- Jika nama kosong (NULL), tampilkan teks (Kosong) -->
                <td><?= !empty($row['nama']) ? htmlspecialchars($row['nama']) : '<span style="color:#aaa;"><i>(Kosong)</i></span>'; ?></td>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['role']); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td>
                    <?php if (strtolower($row['status']) == 'pending'): ?>
                        <a class="btn approve" href="approve_user.php?id=<?= $row['id']; ?>">Approve</a>
                    <?php endif; ?>

                    <?php if (strtolower($row['status']) == 'aktif'): ?>
                        <a class="btn nonaktif" href="nonaktif_user.php?id=<?= $row['id']; ?>">Nonaktif</a>
                    <?php endif; ?>

                    <!-- Tambahan alert konfirmasi sebelum menghapus -->
                    <a class="btn hapus" href="delete_user.php?id=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>