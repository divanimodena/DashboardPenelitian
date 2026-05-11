<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['import'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    // Buka file CSV
    $handle = fopen($file, "r");
    $berhasil = 0;
    $gagal = 0;

    // Lewati baris pertama (header) jika Excel Anda ada judul kolomnya
    fgetcsv($handle, 1000, ","); 

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Petakan kolom (sesuaikan urutan dengan info-box di langkah 2)
        $kode_riset       = mysqli_real_escape_string($conn, $data[0]);
        $kelti            = mysqli_real_escape_string($conn, $data[1]);
        $trl              = (int) $data[2];
        $judul_penelitian = mysqli_real_escape_string($conn, $data[3]);
        $nilai_anggaran   = (int) $data[4];
        $sumber_dana      = mysqli_real_escape_string($conn, $data[5]);
        $bulan            = mysqli_real_escape_string($conn, $data[6]);
        $tahun            = (int) $data[7];
        $status           = 'Diproses'; // Default status

        if (!empty($judul_penelitian)) {
            $query = "INSERT INTO pbj (kode_riset, kelti, trl, judul_penelitian, nilai_anggaran, sumber_dana, bulan, tahun, status) 
                      VALUES ('$kode_riset', '$kelti', '$trl', '$judul_penelitian', '$nilai_anggaran', '$sumber_dana', '$bulan', '$tahun', '$status')";
            
            if (mysqli_query($conn, $query)) {
                $berhasil++;
            } else {
                $gagal++;
            }
        }
    }

    fclose($handle);

    echo "<script>
            alert('Import Selesai! \\nBerhasil: $berhasil data \\nGagal: $gagal data');
            window.location.href = 'data_pbj.php';
          </script>";
}