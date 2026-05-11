<?php
session_start();
include '../config/koneksi.php'; // Sesuaikan path koneksinya ya

if (isset($_POST['simpan_data'])) {
    // 1. Tangkap semua data dari form
    $kelti = mysqli_real_escape_string($conn, trim($_POST['kelti']));
    $trl = (int) $_POST['trl'];
    $kode_riset = mysqli_real_escape_string($conn, trim($_POST['kode_riset']));
    $judul_penelitian = mysqli_real_escape_string($conn, trim($_POST['judul_penelitian']));
    $nilai_anggaran = (int) $_POST['nilai_anggaran'];
    $bulan = mysqli_real_escape_string($conn, trim($_POST['bulan']));
    $tahun = (int) $_POST['tahun'];
    
    // Tangkap sumber dana (Hanya gunakan jika kolom sumber_dana MEMANG ADA di database kamu)
    $sumber_dana = mysqli_real_escape_string($conn, trim($_POST['sumber_dana'])); 

    // 🔥 2. GEMBOK SATPAM ANTI-GANDA
    // Kita cek apakah judul penelitian ini sudah pernah masuk ke database sebelumnya
    $cek_judul = mysqli_query($conn, "SELECT kelti FROM pbj WHERE judul_penelitian = '$judul_penelitian'");
    
    if (mysqli_num_rows($cek_judul) > 0) {
        // Wah, judulnya ketahuan ganda! Kita ambil nama Kelti yang lama
        $data_lama = mysqli_fetch_assoc($cek_judul);
        $kelti_lama = $data_lama['kelti'];
        
        // Munculkan peringatan Pop-up dan tendang balik ke form pengisian
        echo "<script>
                alert('🚨 GAGAL DISIMPAN!\\n\\nJudul penelitian ini sudah terdaftar di Kelti: $kelti_lama.\\n\\nPrinsip Sistem: Satu judul tidak boleh selingkuh ke Kelti lain!');
                window.history.back(); // Otomatis kembali ke form tanpa menghilangkan isian sebelumnya
              </script>";
        exit; // Hentikan proses, jangan simpan ke database!
    } 
    
    // ✅ 3. JIKA AMAN DARI GEMBOK (Judul Belum Ada)
    else {
        // CATATAN: Jika di tabel pbj kamu ADA kolom 'sumber_dana', gunakan query yang BAWAH.
        // Jika TIDAK ADA, hapus kata 'sumber_dana' dan variabel '$sumber_dana' dari query ini.
        
        $query = "INSERT INTO pbj (kelti, trl, kode_riset, judul_penelitian, nilai_anggaran, bulan, tahun) 
                  VALUES ('$kelti', '$trl', '$kode_riset', '$judul_penelitian', '$nilai_anggaran', '$bulan', '$tahun')";
        
        if (mysqli_query($conn, $query)) {
            // Sukses! Arahkan kembali ke halaman tabel PBJ
            header("Location: data_pbj.php?msg=tambah_sukses");
            exit;
        } else {
            die("Gagal menyimpan data ke database: " . mysqli_error($conn));
        }
    }
} else {
    // Jika ada yang iseng buka file ini langsung tanpa lewat form
    header("Location: tambah_pbj.php");
    exit;
}
?>