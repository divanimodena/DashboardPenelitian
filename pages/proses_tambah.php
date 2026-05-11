<?php
// 1. Konfigurasi Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_database_fix"; // Pastikan nama database ini benar sesuai yang kamu buat

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// 2. Menangkap Data yang Dikirim dari Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul_penelitian = $_POST['judul_penelitian'];
    $kode_riset       = $_POST['kode_riset'];
    $jenis_riset      = $_POST['jenis_riset'];
    $kelti            = $_POST['kelti'];
    $tanggal          = $_POST['tanggal'];
    $ppab             = $_POST['ppab'];
    $sap              = $_POST['sap'];
    $po               = $_POST['po'];
    $nilai_realisasi  = $_POST['nilai_realisasi'];
    $status_realisasi = $_POST['status_realisasi'];
    $status_bayar     = $_POST['status_bayar'];

    // 3. Menangani Upload File Dokumen/Gambar
    // Kita buat nama file baru agar tidak ada nama file yang bentrok/sama
    $nama_file_asli = $_FILES['file_upload']['name'];
    $tmp_file       = $_FILES['file_upload']['tmp_name'];
    
    // Menambahkan timestamp di depan nama file
    $nama_file_baru = time() . "_" . str_replace(" ", "_", $nama_file_asli);
    
    // Tentukan lokasi folder penyimpanan (mundur 1 folder dari 'pages', lalu masuk ke 'uploads')
    $direktori_upload = "../uploads/";
    $path_file        = $direktori_upload . $nama_file_baru;

    // Pindahkan file dari tempat sementara ke folder tujuan
    if (move_uploaded_file($tmp_file, $path_file)) {
        
        // 4. Jika upload berhasil, Simpan semua data ke Database
        $query = "INSERT INTO data_penelitian (
                    jenis_riset, kelti, judul_penelitian, kode_riset, 
                    tanggal, ppab, sap, po, nilai_realisasi, 
                    status_realisasi, status_bayar, file_upload
                  ) VALUES (
                    '$jenis_riset', '$kelti', '$judul_penelitian', '$kode_riset', 
                    '$tanggal', '$ppab', '$sap', '$po', '$nilai_realisasi', 
                    '$status_realisasi', '$status_bayar', '$nama_file_baru'
                  )";

        $eksekusi = mysqli_query($conn, $query);

        if ($eksekusi) {
            // Jika berhasil disimpan, langsung alihkan ke home.php dengan parameter sukses
            header("Location: home.php?status=sukses");
            exit;
        } else {
            // Jika query database gagal
            echo "Error Database: " . $query . "<br>" . mysqli_error($conn);
        }

    } else {
        // Jika file gagal diupload ke folder
        echo "<script>
                alert('Gagal mengupload file dokumen!');
                window.history.back();
              </script>";
    }
} else {
    // Jika file ini diakses langsung tanpa lewat form
    header("Location: add
    _penelitian.php");
}

// Tutup koneksi
mysqli_close($conn);
?>