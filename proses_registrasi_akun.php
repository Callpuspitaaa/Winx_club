<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi & Ambil data dari POST
    $required_fields = ["nama_lengkap", "nis", "kelas", "email", "username", "password"];
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            header("location: registrasi_akun.php?error=" . urlencode("Semua field harus diisi."));
            exit;
        }
    }

    $nama_lengkap = trim($_POST["nama_lengkap"]);
    $nis = trim($_POST["nis"]);
    $kelas = trim($_POST["kelas"]);
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // 1. Validasi domain email
    $allowed_domains = [
        '@pplg.smkn1bawang.sch.id',
        '@mplb.smkn1bawang.sch.id',
        '@akl.smkn1bawang.sch.id',
        '@fs.smkn1bawang.sch.id',
        '@te.smkn1bawang.sch.id',
        '@pm.smkn1bawang.sch.id',
        '@ap.smkn1bawang.sch.id',
        '@tjkt.smkn1bawang.sch.id'
    ];
    $valid_email = false;
    foreach ($allowed_domains as $domain) {
        if (str_ends_with(strtolower($email), $domain)) {
            $valid_email = true;
            break;
        }
    }

    if (!$valid_email) {
        header("location: registrasi_akun.php?error=" . urlencode("Pendaftaran harus menggunakan email sekolah dari jurusan yang valid. Contoh: siswa@pplg.smkn1bawang.sch.id"));
        exit;
    }

    // 2. Cek duplikasi username atau email
    $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
    if ($stmt_check = $mysqli->prepare($sql_check)) {
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            header("location: registrasi_akun.php?error=" . urlencode("Username atau email sudah terdaftar."));
            exit;
        }
        $stmt_check->close();
    }

    // 3. Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert user baru dengan role 'siswa'
    $sql_insert = "INSERT INTO users (username, password, nama_lengkap, nis, kelas, email, role) VALUES (?, ?, ?, ?, ?, ?, 'siswa')";
    if ($stmt_insert = $mysqli->prepare($sql_insert)) {
        $stmt_insert->bind_param("ssssss", $username, $hashed_password, $nama_lengkap, $nis, $kelas, $email);
        
        if ($stmt_insert->execute()) {
            header("location: registrasi_akun.php?success=" . urlencode("Registrasi berhasil! Silakan login."));
            exit();
        } else {
            header("location: registrasi_akun.php?error=" . urlencode("Terjadi kesalahan. Silakan coba lagi."));
            exit();
        }
        $stmt_insert->close();
    }
    $mysqli->close();

} else {
    header("location: registrasi_akun.php");
    exit;
}
?>