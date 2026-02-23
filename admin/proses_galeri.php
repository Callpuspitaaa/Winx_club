<?php
session_start();
require_once "../config.php";

// Proteksi & Validasi
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto"])) {

    $judul = trim($_POST['judul']);
    $target_dir = "../img/";
    
    // Buat nama file yang unik untuk menghindari tumpang tindih
    $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
    $unique_file_name = uniqid('img_', true) . '.' . $file_extension;
    $target_file = $target_dir . $unique_file_name;
    
    $uploadOk = 1;
    $imageFileType = strtolower($file_extension);

    // Cek apakah file adalah gambar asli
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if($check === false) {
        $uploadOk = 0; // Bukan gambar
    }

    // Izinkan format tertentu
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0; // Format tidak diizinkan
    }

    // Cek jika $uploadOk adalah 0
    if ($uploadOk == 0) {
        // Handle error, redirect dengan pesan
        header("location: galeri_admin.php?error=upload");
        exit;
    } else {
        // Jika semua ok, coba upload file
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            // File berhasil diupload, sekarang simpan ke DB
            $path_to_db = "img/" . $unique_file_name; // Path yang disimpan di DB
            $uploaded_by = $_SESSION['id'];

            $sql = "INSERT INTO galeri (judul_gambar, path_gambar, uploaded_by) VALUES (?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("ssi", $judul, $path_to_db, $uploaded_by);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Gagal memindahkan file
            header("location: galeri_admin.php?error=move");
            exit;
        }
    }
    $mysqli->close();
}

// Redirect kembali ke halaman galeri admin
header("location: galeri_admin.php");
exit;
?>