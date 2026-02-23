<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config.php";

// Proteksi dan validasi input
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Akses ditolak.");
}

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    die("Parameter tidak valid.");
}

$pendaftar_id = intval($_GET['id']);
$aksi = $_GET['aksi'];

// Ambil user_id dari pendaftar
$sql_get_user = "SELECT user_id, nama_lengkap FROM pendaftar WHERE id = ? AND status = 'pending'";
if ($stmt_get_user = $mysqli->prepare($sql_get_user)) {
    $stmt_get_user->bind_param("i", $pendaftar_id);
    $stmt_get_user->execute();
    $result = $stmt_get_user->get_result();
    
    if ($result->num_rows == 1) {
        $pendaftar = $result->fetch_assoc();
        $user_id_to_update = $pendaftar['user_id'];
        $nama_lengkap_pendaftar = $pendaftar['nama_lengkap'];

        if ($aksi == 'terima') {
            // --- PROSES TERIMA PENDAFTAR ---

            // 1. Update role di tabel users menjadi 'anggota'
            $sql_update_user = "UPDATE users SET role = 'anggota' WHERE id = ?";
            if ($stmt_update_user = $mysqli->prepare($sql_update_user)) {
                $stmt_update_user->bind_param("i", $user_id_to_update);
                $stmt_update_user->execute();
                $stmt_update_user->close();
            }

            // 2. Update status di tabel pendaftar menjadi 'diterima'
            $sql_update_pendaftar = "UPDATE pendaftar SET status = 'diterima' WHERE id = ?";
            if ($stmt_update_pendaftar = $mysqli->prepare($sql_update_pendaftar)) {
                $stmt_update_pendaftar->bind_param("i", $pendaftar_id);
                $stmt_update_pendaftar->execute();
                $stmt_update_pendaftar->close();
            }
            
            $_SESSION['new_user_info'] = "Berhasil! " . htmlspecialchars($nama_lengkap_pendaftar) . " telah diterima sebagai anggota.";

        } elseif ($aksi == 'tolak') {
            // --- PROSES TOLAK PENDAFTAR ---
            $sql_update_pendaftar = "UPDATE pendaftar SET status = 'ditolak' WHERE id = ?";
            if ($stmt_update_pendaftar = $mysqli->prepare($sql_update_pendaftar)) {
                $stmt_update_pendaftar->bind_param("i", $pendaftar_id);
                $stmt_update_pendaftar->execute();
                $stmt_update_pendaftar->close();
            }
            $_SESSION['new_user_info'] = htmlspecialchars($nama_lengkap_pendaftar) . " telah ditolak.";
        }

    } else {
        $_SESSION['new_user_info'] = "Error: Pendaftar tidak ditemukan atau sudah diproses.";
    }
    $stmt_get_user->close();
}

$mysqli->close();

// Redirect kembali ke halaman pendaftar
header("location: pendaftar.php");
exit;

?>