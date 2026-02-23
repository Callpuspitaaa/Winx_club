<?php
session_start();
require_once "../config.php";

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $new_status = '';

    if ($action == 'open') {
        $new_status = 'true';
    } elseif ($action == 'close') {
        $new_status = 'false';
    }

    if (!empty($new_status)) {
        $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = 'recruitment_open'";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $new_status);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect kembali ke dashboard admin
header("location: dashboard.php");
exit;

?>