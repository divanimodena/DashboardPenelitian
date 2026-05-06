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

if (isset($_POST['simpan'])) {
    $kode_riset = mysqli_real_escape_string($conn, $_POST['kode_riset']);
    $kelti = mysqli_real_escape_string($conn, $_POST['kelti']);
    $trl = (int) $_POST['trl'];
    $tahun = (int) $_POST['tahun'];
    $anggaran = (float) $_POST['anggaran'];

    if ($anggaran < 200000000) {
        $status = 'Ditolak';
    } elseif ($anggaran <= 500000000) {
        $status = 'Diproses';
    } else {
        $status = 'Disetujui';
    }

    $query = "INSERT INTO pbj (kode_riset, kelti, trl, nilai_anggaran, tahun, status)
              VALUES ('$kode_riset', '$kelti', '$trl', '$anggaran', '$tahun', '$status')";

    if (mysqli_query($conn, $query)) {
        header("Location: data_pbj.php?msg=tambah");
        exit;
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data PBJ</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-card {
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            max-width: 700px;
            margin: 30px auto;
        }

        .form-title h1 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #1f2f21;
        }

        .form-title p {
            margin: 0 0 24px;
            color: #6b7280;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2f21;
        }

        .form-group input {
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2f7d32;
        }

        .status-preview {
            padding: 12px 14px;
            border-radius: 999px;
            font-weight: 700;
            width: fit-content;
            min-width: 130px;
            text-align: center;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-diproses {
            background: #fef3c7;
            color: #92400e;
        }

        .status-disetujui {
            background: #dcfce7;
            color: #166534;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-simpan,
        .btn-kembali {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn-simpan {
            background: #22c55e;
            color: white;
        }

        .btn-kembali {
            background: #6b7280;
            color: white;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-title">
        <h1>Tambah Data PBJ</h1>
        <p>Isi data di bawah. Status akan otomatis menyesuaikan anggaran.</p>
    </div>

    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Kode Riset</label>
                <input type="text" name="kode_riset" required>
            </div>

            <div class="form-group">
                <label>Kelti</label>
                <input type="text" name="kelti" required>
            </div>

            <div class="form-group">
                <label>TRL</label>
                <input type="number" name="trl" required>
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <input type="number" name="tahun" required>
            </div>

            <div class="form-group full">
                <label>Anggaran</label>
                <input type="number" name="anggaran" id="anggaran" required oninput="updateStatusPreview()">
            </div>

            <div class="form-group full">
                <label>Status Otomatis</label>
                <div id="statusPreview" class="status-preview status-diproses">Diproses</div>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" name="simpan" class="btn-simpan">Simpan</button>
            <a href="data_pbj.php" class="btn-kembali">Kembali</a>
        </div>
    </form>
</div>

<script>
function updateStatusPreview() {
    const anggaran = parseFloat(document.getElementById('anggaran').value) || 0;
    const statusBox = document.getElementById('statusPreview');

    statusBox.className = 'status-preview';

    if (anggaran < 200000000) {
        statusBox.textContent = 'Ditolak';
        statusBox.classList.add('status-ditolak');
    } else if (anggaran <= 500000000) {
        statusBox.textContent = 'Diproses';
        statusBox.classList.add('status-diproses');
    } else {
        statusBox.textContent = 'Disetujui';
        statusBox.classList.add('status-disetujui');
    }
}

updateStatusPreview();
</script>

</body>
</html>