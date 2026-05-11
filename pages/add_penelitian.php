<?php
// Mendeteksi nama file secara otomatis agar menu sidebar yang aktif bisa menyala
$currentPage = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Penelitian - PPKS</title>
    
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* 2. SAMAKAN CSS OVERRIDE SIDEBAR DENGAN YANG ADA DI DASHBOARD.PHP */
        .sidebar { display: flex !important; flex-direction: column !important; justify-content: flex-start !important; }
        .sidebar-menu { margin-top: 20px !important; display: flex !important; flex-direction: column !important; gap: 5px !important; flex-grow: 0 !important; }
        .menu-item { margin-bottom: 0 !important; padding: 10px 15px !important; display: flex !important; align-items: center !important; line-height: 1.2 !important; }
        .menu-item i { width: 25px; }
        .sidebar-bottom { margin-top: auto !important; padding-top: 20px; }

        /* --- FORM STYLE KHUSUS HALAMAN INI --- */
        .main-content {
            padding: 40px;
        }
        
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 26px; font-weight: 700; color: #1f2937; }
        .page-subtitle { font-size: 15px; color: #6b7280; margin-top: 4px; }

        /* Card Form */
        .form-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #16a34a;
            margin-bottom: 16px;
            margin-top: 24px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        .form-section-title:first-child { margin-top: 0; }

        /* Grid Layout untuk Form */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 Kolom */
            gap: 20px;
        }
        .full-width { grid-column: span 2; }

        /* Form Controls */
        .form-group { display: flex; flex-direction: column; }
        .form-label {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            height: 48px;
            padding: 0 14px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 15px;
            color: #1f2937;
            background: #fff;
            outline: none;
            transition: 0.3s ease;
            font-family: inherit;
        }
        .form-control:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
        }
        .form-control[type="file"] {
            padding: 10px 14px;
        }

        /* Buttons */
        .btn-container { display: flex; gap: 12px; margin-top: 30px; justify-content: flex-end; }
        
        .btn-primary {
            background: #16a34a; color: #fff; border: none; border-radius: 12px;
            padding: 12px 22px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s ease;
        }
        .btn-primary:hover { background: #15803d; }

        .btn-secondary {
            background: #e5e7eb; color: #1f2937; border: none; border-radius: 12px;
            padding: 12px 20px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s ease;
            text-decoration: none;
        }
        .btn-secondary:hover { background: #d1d5db; }
    </style>
</head>
<body>

    <div class="dashboard-layout">

        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <h1 class="page-title">Tambah Data Penelitian</h1>
                <p class="page-subtitle">Silakan isi formulir di bawah ini untuk menambahkan data anggaran penelitian baru.</p>
            </div>

            <div class="form-card">
                <form action="proses_tambah.php" method="POST" enctype="multipart/form-data" id="formPenelitian">
                    
                    <h3 class="form-section-title">Informasi Utama</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Judul Penelitian</label>
                            <input type="text" name="judul_penelitian" class="form-control" placeholder="Masukkan judul penelitian lengkap" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kode Riset</label>
                            <input type="text" name="kode_riset" class="form-control" placeholder="Cth: RS-2026-001" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Jenis Riset</label>
                            <select name="jenis_riset" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Jenis Riset --</option>
                                <option value="Internal">Riset Internal</option>
                                <option value="Eksternal">Riset Eksternal</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Kelompok Peneliti (Kelti)</label>
                            <select name="kelti" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Kelti --</option>
                                <option value="Bio Teknologi dan Bio industri">Bio Teknologi dan Bio industri</option>
                                <option value="Ilmu Tanah dan Agronomi">Ilmu Tanah dan Agronomi</option>
                                <option value="Mekanisasi pasca panen dan konservasi lingkungan">Mekanisasi pasca panen dan konservasi lingkungan</option>
                                <option value="Pemuliaan Tanaman">Pemuliaan Tanaman</option>
                                <option value="Proteksi Tanaman">Proteksi Tanaman</option>
                                <option value="Sosial Ekonomi">Sosial Ekonomi</option>
                                <option value="Kelapa">Kelapa</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="form-section-title">Detail Pengadaan & Anggaran</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No. PPAB</label>
                            <input type="text" name="ppab" class="form-control" placeholder="Cth: 0029/01/2026/PPAB..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No. SAP</label>
                            <input type="text" name="sap" class="form-control" placeholder="Cth: 1100393185" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">No. PO</label>
                            <input type="text" name="po" class="form-control" placeholder="Cth: 001.4/01/2026/PO..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nilai Realisasi (Rp)</label>
                            <input type="text" id="inputRupiah" name="nilai_realisasi" class="form-control" placeholder="Cth: 27.079.000" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status Realisasi</label>
                            <select name="status_realisasi" class="form-control" required>
                                <option value="Proses" selected>Proses</option>
                                <option value="Sudah">Sudah</option>
                                <option value="Belum">Belum</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status Bayar</label>
                            <select name="status_bayar" class="form-control" required>
                                <option value="Belum" selected>Belum</option>
                                <option value="Sudah">Sudah</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Upload Dokumen (PDF/Image)</label>
                            <input type="file" name="file_upload" class="form-control" accept=".pdf, .png, .jpg, .jpeg" required>
                        </div>
                    </div>

                    <div class="btn-container">
                        <a href="home.php" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Simpan Data</button>
                    </div>

                </form>
            </div>
        </main>
        
    </div>

    <script>
        const inputRupiah = document.getElementById('inputRupiah');
        const formPenelitian = document.getElementById('formPenelitian');

        // Event saat pengguna mengetik angka
        inputRupiah.addEventListener('input', function(e) {
            // Hapus semua huruf/karakter yang bukan angka
            let val = this.value.replace(/\D/g, '');
            // Tambahkan titik setiap 3 digit angka
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });

        // Event sebelum data dikirim ke proses_tambah.php
        formPenelitian.addEventListener('submit', function() {
            // Hapus kembali titiknya agar database menerima nilai mentah (contoh: 27079000)
            inputRupiah.value = inputRupiah.value.replace(/\./g, '');
        });
    </script>

</body>
</html>