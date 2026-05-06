<?php
if (!function_exists('tambahAktivitas')) {
    function tambahAktivitas($conn, $judul, $deskripsi, $ikon = '📌')
    {
        $judul = mysqli_real_escape_string($conn, $judul);
        $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
        $ikon = mysqli_real_escape_string($conn, $ikon);

        $sql = "INSERT INTO aktivitas (judul, deskripsi, ikon)
                VALUES ('$judul', '$deskripsi', '$ikon')";

        return mysqli_query($conn, $sql);
    }
}
?>