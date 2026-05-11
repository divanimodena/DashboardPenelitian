<aside class="sidebar">
    <div>
        <div class="sidebar-logo">
            <h2>Dashboard</h2>
            <span>Penelitian</span>

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

            

            <a href="pengguna.php" class="menu-item <?= ($currentPage == 'pengguna.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-group"></i>
                <span>Pengguna</span>
            </a>
        </nav>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <a href="logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>

    </div>
</aside>