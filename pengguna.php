<?php
session_start();

if (($_SESSION['role'] ?? 'user') !== 'Admin') {
    header("Location: dashboard.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
date_default_timezone_set('Asia/Jakarta');
include 'config/koneksi.php';

$usernameLogin = ucfirst($_SESSION['username'] ?? 'Admin');

/* HAPUS USER */
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    header("Location: pengguna.php");
    exit;
}

/* TAMBAH / EDIT USER */
if (isset($_POST['simpan_user'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));
    $password = trim($_POST['password'] ?? '');

    if ($id > 0) {
        if ($password != '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', password='$passwordHash', status='$status' WHERE id=$id";
        } else {
            $query = "UPDATE users SET username='$username', status='$status' WHERE id=$id";
        }
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, status) VALUES ('$username', '$passwordHash', '$status')";
    }

    mysqli_query($conn, $query);
    header("Location: pengguna.php");
    exit;
}

/* MODE EDIT */
$editMode = false;
$editData = [
    'id' => '',
    'username' => '',
    'status' => 'active'
];

if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];
    $resultEdit = mysqli_query($conn, "SELECT * FROM users WHERE id = $idEdit LIMIT 1");

    if ($resultEdit && mysqli_num_rows($resultEdit) > 0) {
        $editMode = true;
        $editData = mysqli_fetch_assoc($resultEdit);
    }
}

/* AMBIL DATA */
$rowsUsers = [];
$totalUser = 0;
$totalPending = 0;
$totalActive = 0;

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rowsUsers[] = $row;
        $totalUser++;

        if (($row['status'] ?? '') === 'pending') {
            $totalPending++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengguna</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(248,251,247,0.92));
            border: 1px solid rgba(47, 107, 61, 0.08);
            box-shadow: 0 16px 40px rgba(31, 45, 31, 0.08);
            border-radius: 28px;
            padding: 24px;
        }

        .user-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .user-title h2 {
            font-size: 28px;
            color: #1f4d2e;
            margin-bottom: 6px;
        }

        .user-title p {
            color: #6d786d;
            font-size: 14px;
        }

        .user-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .user-search {
            min-width: 260px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 12px 14px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .user-search i {
            color: #6c7b6d;
        }

        .user-search input {
            border: none;
            outline: none;
            width: 100%;
            background: transparent;
            font-size: 14px;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            border-radius: 14px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #4facfe, #43e97b);
            color: white;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(79, 172, 254, 0.22);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: rgba(255,255,255,0.85);
            border-radius: 22px;
            padding: 18px;
            border: 1px solid rgba(47, 107, 61, 0.06);
            box-shadow: 0 12px 26px rgba(0,0,0,0.05);
        }

        .summary-card p {
            color: #6d786d;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .summary-card h3 {
            font-size: 26px;
            color: #1f2f21;
        }

        .form-card {
            margin-bottom: 20px;
            background: rgba(255,255,255,0.88);
            border-radius: 22px;
            padding: 20px;
            border: 1px solid rgba(47, 107, 61, 0.06);
            box-shadow: 0 12px 26px rgba(0,0,0,0.05);
        }

        .form-card h3 {
            margin-bottom: 16px;
            color: #1f4d2e;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #304031;
        }

        .form-group input,
        .form-group select {
            border: 1px solid #d9e3d7;
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
        }

        .form-actions {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .save-btn, .cancel-btn {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .save-btn {
            background: linear-gradient(135deg, #2d6a38, #43e97b);
            color: white;
        }

        .cancel-btn {
            background: #eef2ee;
            color: #304031;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 22px;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            background: white;
            border-radius: 22px;
        }

        .user-table thead th {
            background: linear-gradient(135deg, #2d6a38, #3f8749);
            color: white;
            font-weight: 700;
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }

        .user-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf1ea;
            font-size: 14px;
            color: #304031;
            vertical-align: middle;
        }

        .user-table tbody tr:hover {
            background: #f9fcf8;
        }

        .status-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mini-btn {
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        @media (max-width: 900px) {
            .summary-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .user-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .user-actions {
                width: 100%;
            }

            .user-search {
                min-width: unset;
                width: 100%;
            }

            .add-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-logo">
                <h2>Dashboard</h2>
                <span>Anggaran</span>
                <div class="sidebar-subtitle">
                    <small>Pusat Penelitian</small>
                    <strong>Kelapa Sawit</strong>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
                <a href="statistik.php" class="menu-item <?= ($currentPage == 'statistik.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Statistik</span>
                </a>
                <a href="data_pbj.php" class="menu-item <?= ($currentPage == 'data_pbj.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Data PBJ</span>
                </a>
                <a href="anggaran.php" class="menu-item <?= ($currentPage == 'anggaran.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Anggaran</span>
                </a>
                <a href="pengguna.php" class="menu-item <?= ($currentPage == 'pengguna.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Pengguna</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <p class="welcome-label">User Overview</p>
                <h1>Pengguna</h1>
                <span>Kelola data pengguna dashboard.</span>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search here">
                </div>

                <div class="top-actions">
                    
                </div>
            </div>
        </header>

        <section class="page-card">
            <div class="user-toolbar">
                <div class="user-title">
                    <h2>Daftar Pengguna</h2>
                    <p>Data pengguna yang memiliki akses ke sistem.</p>
                </div>

                <div class="user-actions">
                    <div class="user-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="userSearch" placeholder="Cari username, email, status">
                    </div>

            
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <p>Total Pengguna</p>
                    <h3><?= $totalUser; ?></h3>
                </div>

                <div class="summary-card">
                    <p>User Active</p>
                    <h3><?= $totalActive; ?></h3>
                </div>

                <div class="summary-card">
                    <p>User Pending</p>
                    <h3><?= $totalPending; ?></h3>
                </div>
            </div>

            <div class="form-card" id="formUser">
                <h3><?= $editMode ? 'Edit Pengguna' : 'Tambah Pengguna'; ?></h3>

                <form method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']); ?>">

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" required value="<?= htmlspecialchars($editData['username']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Password <?= $editMode ? '(kosongkan jika tidak diubah)' : ''; ?></label>
                            <input type="password" name="password" <?= $editMode ? '' : 'required'; ?>>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="active" <?= ($editData['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="pending" <?= ($editData['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="rejected" <?= ($editData['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="simpan_user" class="save-btn">
                            <?= $editMode ? 'Update User' : 'Simpan User'; ?>
                        </button>

                        <?php if ($editMode): ?>
                            <a href="pengguna.php" class="cancel-btn">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

    <h2 style="color:#1f5c2e;">Daftar Pengguna</h2>

    <a href="tambah_pengguna.php" class="add-btn">
        + Tambah Pengguna
    </a>

</div>
            <div class="table-wrap">
                <table class="user-table" id="userTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rowsUsers)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($rowsUsers as $row) : ?>
                                <?php
                                $status = $row['status'] ?? 'pending';
                                $statusClass = 'status-pending';

                                if ($status === 'active') $statusClass = 'status-active';
                                if ($status === 'rejected') $statusClass = 'status-rejected';
                                ?>
                                <tr data-search="<?= strtolower(($row['username'] ?? '') . ' ' . ($row['email'] ?? '') . ' ' . $status); ?>">
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['username'] ?? ''); ?></td>
                                    <td>
                                        <span class="status-badge <?= $statusClass; ?>">
                                            <?= ucfirst(htmlspecialchars($status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="?edit=<?= $row['id']; ?>#formUser" class="mini-btn btn-edit">Edit</a>
                                            <a href="?hapus=<?= $row['id']; ?>" class="mini-btn btn-delete" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada data pengguna</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userSearch = document.getElementById('userSearch');
    const rows = document.querySelectorAll('#userTable tbody tr');

    if (userSearch) {
        userSearch.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();

            rows.forEach(row => {
                const text = (row.getAttribute('data-search') || row.textContent).toLowerCase();
                row.style.display = keyword === '' || text.includes(keyword) ? '' : 'none';
            });
        });
    }
});
</script>

</body>
</html>