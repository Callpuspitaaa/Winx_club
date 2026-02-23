<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container navbar">
        <a href="index.php" class="logo">
            <img src="logo.png" alt="BDC Logo">
            <span>BDC</span>
        </a>
    </div>
</header>

<button id="menu-toggle">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
</button>

<nav id="menu-nav">
    <ul>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <?php // --- MENU UNTUK PENGGUNA YANG SUDAH LOGIN --- ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#tentang">Tentang Kami</a></li>
            <li><a href="galeri.php">Galeri</a></li>
            
            <?php if ($_SESSION["role"] === 'admin'): ?>
                <li><a href="admin/dashboard.php">Dashboard Admin</a></li>
            <?php else: ?>
                <li><a href="pendaftaran.php">Join Ekskul</a></li>
            <?php endif; ?>

            <li><a href="logout.php">Logout</a></li>

        <?php else: ?>
            <?php // --- MENU UNTUK PENGUNJUNG UMUM --- ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#tentang">Tentang Kami</a></li>
            <li><a href="galeri.php">Galeri</a></li>
            <li><a href="pendaftaran.php">Join Ekskul</a></li>
            <li><a href="registrasi_akun.php">Registrasi Akun</a></li>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>
