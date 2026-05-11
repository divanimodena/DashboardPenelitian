<?php
// Deteksi halaman aktif
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-container">
        <div class="sidebar-logo">
            <h2>Dashboard</h2>
            <span>Penelitian</span>
            <div class="sidebar-subtitle">
                <small>Pusat Penelitian</small>
                <strong>Kelapa Sawit</strong>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="home.php" class="menu-item <?= ($currentPage == 'home.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>

            <a href="add_penelitian.php" class="menu-item <?= ($currentPage == 'add_penelitian.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>Add Penelitian</span>
            </a>

            <a href="pengaturan_anggaran.php" class="menu-item <?= ($currentPage == 'pengaturan_anggaran.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-gear"></i>
                <span>Pengaturan Anggaran</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>