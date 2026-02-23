<?php
session_start();
require_once "../config.php";

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['jumlah_iuran']) && is_numeric($_POST['jumlah_iuran'])) {
        $jumlah_iuran_baru = intval($_POST['jumlah_iuran']);

        // Update nilai di database
        $sql = "UPDATE pengaturan_iuran SET jumlah_iuran = ? WHERE id = 1";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $jumlah_iuran_baru);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Redirect kembali ke halaman kas
header("location: kas.php");
exit;
?>