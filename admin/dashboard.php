<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah login dan apakah rolenya admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] !== 'admin') {
    header("location: /login.php");
    exit;
}

require_once "../config.php";

// Ambil status pendaftaran saat ini
$sql = "SELECT setting_value FROM settings WHERE setting_key = 'recruitment_open'";
$result = $mysqli->query($sql);
$recruitment_status = $result->fetch_assoc()['setting_value'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        
        <div class="content-area">
            <h2>Status Pendaftaran Ekskul</h2>
            <p>Status Pendaftaran Saat Ini: 
                <strong style="color: <?php echo ($recruitment_status == 'true') ? 'green' : 'red'; ?>;">
                    <?php echo ($recruitment_status == 'true') ? 'DIBUKA' : 'DITUTUP'; ?>
                </strong>
            </p>
            <form action="toggle_pendaftaran.php" method="POST" style="margin-top: 1rem;">
                <?php if ($recruitment_status == 'true'): ?>
                    <button type="submit" name="action" value="close" class="btn btn-primary" style="background-color: red;">Tutup Pendaftaran</button>
                <?php else: ?>
                    <button type="submit" name="action" value="open" class="btn btn-primary" style="background-color: green;">Buka Pendaftaran</button>
                <?php endif; ?>
            </form>
        </div>
    </main>
</div>

</body>
</html>
