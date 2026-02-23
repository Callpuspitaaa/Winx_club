<?php
// File: admin/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="logo-container">
        <img src="../logo.png" alt="BDC Logo">
        <h2>Admin BDC</h2>
    </div>
    <ul>
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="pendaftar.php" class="<?php echo ($current_page == 'pendaftar.php') ? 'active' : ''; ?>">Pendaftar Ekskul</a></li>
        <li><a href="absensi.php" class="<?php echo ($current_page == 'absensi.php') ? 'active' : ''; ?>">Manajemen Absensi</a></li>
        <li><a href="kas.php" class="<?php echo ($current_page == 'kas.php') ? 'active' : ''; ?>">Manajemen Keuangan</a></li>
        <li><a href="galeri_admin.php" class="<?php echo ($current_page == 'galeri_admin.php') ? 'active' : ''; ?>">Manajemen Galeri</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</aside>
