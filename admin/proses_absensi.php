<?php
session_start();
require_once "../config.php";

// Proteksi
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status']) && isset($_POST['tanggal'])) {

    $tanggal = $_POST['tanggal'];
    $statuses = $_POST['status'];

    // Siapkan statement. Menggunakan INSERT ... ON DUPLICATE KEY UPDATE lebih efisien.
    $sql = "INSERT INTO absensi (user_id, tanggal, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Loop melalui setiap status yang dikirim dari form
        foreach ($statuses as $user_id => $status) {
            $uid = intval($user_id);
            $current_status = trim($status);

            // Bind parameter dan eksekusi untuk setiap anggota
            $stmt->bind_param("iss", $uid, $tanggal, $current_status);
            $stmt->execute();
        }
        $stmt->close();
    }
    $mysqli->close();
}

// Redirect kembali ke halaman absensi untuk tanggal yang sama
header("location: absensi.php?tanggal=" . urlencode($_POST['tanggal']));
exit;

?>