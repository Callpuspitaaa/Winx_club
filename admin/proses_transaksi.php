<?php
session_start();
require_once "../config.php";

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $deskripsi = trim($_POST['deskripsi']);
    $pemasukan = intval($_POST['pemasukan']);
    $pengeluaran = intval($_POST['pengeluaran']);
    $created_by = $_SESSION['id'];

    // Validasi sederhana
    if (empty($tanggal) || empty($deskripsi) || ($pemasukan == 0 && $pengeluaran == 0)) {
        header("location: kas.php?tab=transaksi&error=" . urlencode("Data tidak boleh kosong."));
        exit;
    }

    $sql = "INSERT INTO kas (tanggal, deskripsi, pemasukan, pengeluaran, created_by) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssiii", $tanggal, $deskripsi, $pemasukan, $pengeluaran, $created_by);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
}

// Redirect kembali ke halaman kas dengan tab transaksi aktif
header("location: kas.php?tab=transaksi");
exit;
?>