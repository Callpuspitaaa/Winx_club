<?php
session_start();
require_once "config.php";

// Fungsi untuk menampilkan error dan menghentikan skrip
function die_with_error($message, $location = "pendaftaran.php") {
    header("location: $location?error=" . urlencode($message));
    exit;
}

// Cek jika pengguna belum login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    die_with_error("Anda harus login untuk mendaftar ekskul.", "login.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['id'];

    // Validasi status pendaftaran
    $sql_check_setting = "SELECT setting_value FROM settings WHERE setting_key = 'recruitment_open'";
    $result_setting = $mysqli->query($sql_check_setting);
    $recruitment_open = ($result_setting->fetch_assoc()['setting_value'] == 'true');

    if (!$recruitment_open) {
        die_with_error("Pendaftaran sedang ditutup.");
    }

    // Validasi apakah pengguna sudah pernah mendaftar
    $sql_check_reg = "SELECT id FROM pendaftar WHERE user_id = ?";
    $stmt_check = $mysqli->prepare($sql_check_reg);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        die_with_error("Anda sudah pernah mendaftar sebelumnya.");
    }
    $stmt_check->close();

    // Proses upload video
    $video_path = null;
    if (isset($_FILES["video_dance"]) && $_FILES["video_dance"]["error"] == 0) {
        $upload_dir = "uploads/videos/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = uniqid() . "-" . basename($_FILES["video_dance"]["name"]);
        $target_file = $upload_dir . $file_name;
        $video_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi tipe file (hanya video)
        $allowed_types = ["mp4", "mov", "avi", "mkv", "webm"];
        if (!in_array($video_file_type, $allowed_types)) {
            die_with_error("Format video tidak didukung. Silakan unggah format mp4, mov, avi, mkv, atau webm.");
        }

        // Validasi ukuran file (misal, maks 50MB)
        if ($_FILES["video_dance"]["size"] > 50 * 1024 * 1024) {
            die_with_error("Ukuran video terlalu besar. Maksimal 50MB.");
        }

        if (move_uploaded_file($_FILES["video_dance"]["tmp_name"], $target_file)) {
            $video_path = $target_file; // Path relatif dari root proyek
        } else {
            die_with_error("Terjadi kesalahan saat mengunggah video Anda.");
        }
    } else {
        die_with_error("Video dance wajib diunggah.");
    }

    // Ambil data dari form
    $no_hp = trim($_POST["no_hp"]);
    $alasan_bergabung = trim($_POST["alasan_bergabung"]);

    // Ambil data pengguna dari tabel users
    $user_data = null;
    $sql_user = "SELECT nama_lengkap, nis, kelas FROM users WHERE id = ?";
    if($stmt_user = $mysqli->prepare($sql_user)){
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user_data = $stmt_user->get_result()->fetch_assoc();
        $stmt_user->close();
    }

    if ($user_data === null) {
        die_with_error("Gagal mengambil data pengguna.");
    }

    // Update data NIS dan Kelas jika diperlukan
    $nis_from_form = trim($_POST['nis']);
    $kelas_from_form = trim($_POST['kelas']);
    if (empty($user_data['nis']) || empty($user_data['kelas'])) {
        $sql_update_user = "UPDATE users SET nis = ?, kelas = ? WHERE id = ?";
        if($stmt_update = $mysqli->prepare($sql_update_user)){
            $stmt_update->bind_param("ssi", $nis_from_form, $kelas_from_form, $user_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // Siapkan statement INSERT ke tabel pendaftar
    $sql = "INSERT INTO pendaftar (user_id, nama_lengkap, nis, kelas, no_hp, alasan_bergabung, video_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("issssss", $user_id, $user_data['nama_lengkap'], $nis_from_form, $kelas_from_form, $no_hp, $alasan_bergabung, $video_path);
        
        if ($stmt->execute()) {
            header("location: sukses_pendaftaran.html");
            exit();
        } else {
            // Jika gagal, hapus video yang sudah terupload
            if ($video_path && file_exists($video_path)) {
                unlink($video_path);
            }
            die_with_error("Terjadi kesalahan database. Silakan coba lagi.");
        }
        $stmt->close();
    }
    $mysqli->close();

} else {
    header("location: pendaftaran.php");
    exit;
}
?>