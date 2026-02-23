<?php
session_start();
require_once "../config.php";

// Proteksi
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tanggal_pertemuan'])) {

    $tanggal_pertemuan = $_POST['tanggal_pertemuan'];
    $status_bayar = isset($_POST['status_bayar']) ? $_POST['status_bayar'] : [];

    // Ambil semua ID anggota
    $sql_users = "SELECT id FROM users WHERE role = 'anggota'";
    $result_users = $mysqli->query($sql_users);
    $anggota_ids = [];
    while($row = $result_users->fetch_assoc()){
        $anggota_ids[] = $row['id'];
    }

    // Siapkan statement
    $sql = "INSERT INTO iuran (user_id, tanggal_pertemuan, status_pembayaran) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status_pembayaran = VALUES(status_pembayaran)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Loop melalui semua anggota, bukan hanya yang di-ceklist
        foreach ($anggota_ids as $user_id) {
            // Tentukan status berdasarkan apakah ID mereka ada di array POST `status_bayar`
            $status = isset($status_bayar[$user_id]) && $status_bayar[$user_id] == 'lunas' ? 'lunas' : 'belum_lunas';
            
            $stmt->bind_param("iss", $user_id, $tanggal_pertemuan, $status);
            $stmt->execute();
        }
        $stmt->close();
    }
    $mysqli->close();
}

// Redirect kembali ke halaman kas dengan tab iuran aktif
header("location: kas.php?tab=iuran&tanggal=" . urlencode($_POST['tanggal_pertemuan']));
exit;

?>