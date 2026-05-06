<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

/* UPDATE STATUS */
if (isset($_POST['update_status'])) {
    $id = (int) $_POST['id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE pbj SET status='$status' WHERE id=$id");
    header("Location: data_pbj.php?msg=status");
    exit;
}

/* HAPUS DATA */
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];
    mysqli_query($conn, "DELETE FROM pbj WHERE id=$id");
    header("Location: data_pbj.php?msg=hapus");
    exit;
}

/* AMBIL DATA */
$result = mysqli_query($conn, "SELECT * FROM pbj ORDER BY id ASC");

$totalDataPBJ = 0;
$totalDiproses = 0;
$totalDisetujui = 0;
$rowsPBJ = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rowsPBJ[] = $row;
        $totalDataPBJ++;

        $statusSekarang = isset($row['status']) ? $row['status'] : 'Diproses';

        if ($statusSekarang == 'Diproses') {
            $totalDiproses++;
        } elseif ($statusSekarang == 'Disetujui') {
            $totalDisetujui++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PBJ</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .menu-item:hover,
        .menu-item.active,
        .top-actions button:hover,
        .quick-btn:hover,
        .stat-card:hover,
        .icon-btn:hover,
        .link-card:hover,
        .add-btn:hover,
        .logout-btn:hover,
        .status-dropdown:hover,
        .edit:hover,
        .hapus-btn:hover {
            transform: none !important;
            box-shadow: none !important;
            transition: none !important;
        }

        .page-card,
        .summary-card,
        .topbar,
        .top-action,
        .search-box,
        .table-wrap,
        .tabel-pbj,
        .sidebar,
        .main-content,
        .page-card *,
        .summary-card *,
        .topbar *,
        .top-action *,
        .search-box *,
        .table-wrap *,
        .tabel-pbj *,
        .sidebar *,
        .main-content * {
            transition: none !important;
            animation: none !important;
            transform: none !important;
        }

        .page-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(248,251,247,0.92));
            border: 1px solid rgba(47, 107, 61, 0.08);
            box-shadow: 0 16px 40px rgba(31, 45, 31, 0.08);
            border-radius: 28px;
            padding: 24px;
        }

        .add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #4facfe, #43e97b);
            color: white;
            font-weight: 700;
            cursor: pointer;
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
            margin: 0;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 22px;
        }

        .tabel-pbj {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
        }

        .tabel-pbj th,
        .tabel-pbj td {
            padding: 14px 12px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .tabel-pbj th {
            background: #2f7d32;
            color: white;
            font-weight: 600;
        }

        .notif {
            width: fit-content;
            max-width: 400px;
            margin-bottom: 18px;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(0,0,0,0.08);
            animation: fadeIn 0.3s ease;
        }

        .notif.sukses {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .notif.gagal {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .notif.info {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-dropdown {
            border: none;
            outline: none;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            min-width: 115px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .status-dropdown.hijau {
            background: #dcfce7;
            color: #166534;
        }

        .status-dropdown.kuning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dropdown.merah {
            background: #fee2e2;
            color: #991b1b;
        }

        td.aksi {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .edit,
        .hapus-btn {
            display: inline-block;
            min-width: 55px;
            text-align: center;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .edit {
            background: #3b82f6;
        }

        .hapus-btn {
            background: #ef4444;
        }

        .edit:hover {
            background: #2563eb;
        }

        .hapus-btn:hover {
            background: #dc2626;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            width: 380px;
            max-width: 90%;
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(0,0,0,0.2);
            text-align: center;
        }

        .modal-box h3 {
            margin-bottom: 10px;
            color: #1f2f21;
        }

        .modal-box p {
            margin-bottom: 20px;
            color: #4b5563;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .modal-hapus {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-batal {
            background: #e5e7eb;
            color: #111827;
            border: none;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }


        .top-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            width: 330px;
            max-width: 100%;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 14px;
        }

        .search-box input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1px solid #d7ded7;
            border-radius: 12px;
            outline: none;
            font-size: 14px;
            background: #fff;
        }

        .search-box input:focus {
            border-color: #43e97b;
            box-shadow: 0 0 0 4px rgba(67, 233, 123, 0.15);
        }

        .no-search-result {
            display: none;
        }
        @media (max-width: 900px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .top-action {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }

            td.aksi {
                flex-direction: column;
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
                <p class="welcome-label">PBJ Overview</p>
                <h1>Data PBJ</h1>
                <span>Kelola data PBJ</span>
            </div>
        </header>

        <section class="page-card">

            <?php if (isset($_GET['pesan'])): ?>
                <?php if ($_GET['pesan'] == 'update_sukses'): ?>
                    <div class="notif sukses" id="notifBox">Data berhasil diupdate</div>
                <?php elseif ($_GET['pesan'] == 'update_gagal'): ?>
                    <div class="notif gagal" id="notifBox">Data gagal diupdate</div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] == 'status'): ?>
                    <div class="notif info" id="notifBox">Status berhasil diperbarui</div>
                <?php elseif ($_GET['msg'] == 'hapus'): ?>
                    <div class="notif gagal" id="notifBox">Data berhasil dihapus</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="top-action">
                <a href="tambah_pbj.php" class="add-btn">+ Tambah Data</a>

                <div class="search-box">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Cari data PBJ..."
                        onkeyup="searchTable()"
                    >
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <p>Total Data PBJ</p>
                    <h3><?= $totalDataPBJ; ?></h3>
                </div>

                <div class="summary-card">
                    <p>Diproses</p>
                    <h3><?= $totalDiproses; ?></h3>
                </div>

                <div class="summary-card">
                    <p>Disetujui</p>
                    <h3><?= $totalDisetujui; ?></h3>
                </div>
            </div>

            <div class="table-wrap">
                <table class="tabel-pbj">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Riset</th>
                            <th>Kelti</th>
                            <th>TRL</th>
                            <th>Anggaran</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($rowsPBJ as $row) : ?>
                            <?php
                                $statusSekarang = isset($row['status']) ? $row['status'] : 'Diproses';

                                $statusClass = 'kuning';
                                if ($statusSekarang == 'Disetujui') {
                                    $statusClass = 'hijau';
                                } elseif ($statusSekarang == 'Ditolak') {
                                    $statusClass = 'merah';
                                }

                                $nilaiAnggaran = 0;
                                if (isset($row['nilai_anggaran'])) {
                                    $nilaiAnggaran = $row['nilai_anggaran'];
                                } elseif (isset($row['anggaran'])) {
                                    $nilaiAnggaran = $row['anggaran'];
                                }
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['kode_riset']); ?></td>
                                <td><?= htmlspecialchars($row['kelti']); ?></td>
                                <td><?= htmlspecialchars($row['trl']); ?></td>
                                <td>Rp <?= number_format((float)$nilaiAnggaran, 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($row['tahun']); ?></td>

                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">

                                        <select name="status" onchange="this.form.submit()" class="status-dropdown <?= $statusClass; ?>">
                                            <option value="Ditolak" <?= $statusSekarang == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                            <option value="Diproses" <?= $statusSekarang == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                            <option value="Disetujui" <?= $statusSekarang == 'Disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                                        </select>
                                    </form>
                                </td>

                                <td class="aksi">
                                    <a href="edit_pbj.php?id=<?= $row['id']; ?>" class="edit">Edit</a>
                                    <button type="button" class="hapus-btn" onclick="openDeleteModal(<?= $row['id']; ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($rowsPBJ)) : ?>
                            <tr>
                                <td colspan="8">Belum ada data PBJ.</td>
                            </tr>
                        <?php endif; ?>

                        <tr id="noSearchResult" class="no-search-result">
                            <td colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div id="deleteModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Konfirmasi Hapus</h3>
        <p>Yakin hapus data ini?</p>

        <form method="POST">
            <input type="hidden" name="id" id="deleteId">
            <div class="modal-actions">
                <button type="submit" name="hapus" class="modal-hapus">Ya, Hapus</button>
                <button type="button" class="modal-batal" onclick="closeDeleteModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('deleteModal');
    if (e.target === modal) {
        closeDeleteModal();
    }
});

const notif = document.getElementById('notifBox');
if (notif) {
    setTimeout(() => {
        notif.style.display = 'none';
    }, 2000);
}

function searchTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.tabel-pbj tbody tr:not(#noSearchResult)');
    const noResult = document.getElementById('noSearchResult');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const match = text.includes(input);
        row.style.display = match ? '' : 'none';

        if (match) {
            visibleCount++;
        }
    });

    if (noResult) {
        noResult.style.display = visibleCount === 0 ? 'table-row' : 'none';
    }
}
</script>

</body>
</html>