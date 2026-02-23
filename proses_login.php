<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file config.php
require_once "config.php";

// Cek jika pengguna sudah login, redirect ke halaman dashboard yang sesuai
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["role"] == 'admin'){
        header("location: admin/dashboard.php");
    } else {
        header("location: anggota/dashboard.php");
    }
    exit;
}

// Inisialisasi variabel
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Proses data form ketika disubmit
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Cek jika username kosong
    if(empty(trim($_POST["username"]))){ 
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Cek jika password kosong
    if(empty(trim($_POST["password"]))){ 
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validasi kredensial
    if(empty($username_err) && empty($password_err)){
        // Siapkan statement SELECT
        $sql = "SELECT id, username, password, role, nama_lengkap FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variabel ke statement
            $stmt->bind_param("s", $param_username);
            
            // Set parameter
            $param_username = $username;
            
            // Eksekusi statement
            if($stmt->execute()){
                // Simpan hasil
                $stmt->store_result();
                
                // Cek jika username ada, lalu verifikasi password
                if($stmt->num_rows == 1){
                    // Bind hasil ke variabel
                    $stmt->bind_result($id, $username, $hashed_password, $role, $nama_lengkap);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password benar, sesi sudah dimulai di atas
                            
                            // Simpan data di session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;
                            $_SESSION["nama_lengkap"] = $nama_lengkap;                            
                            
                            // Menulis dan menutup sesi sebelum redirect
                            session_write_close();

                            // Redirect pengguna berdasarkan role
                            if($role == 'admin'){
                                header("location: admin/dashboard.php");
                            } else {
                                // Pengguna biasa diarahkan ke halaman utama setelah login
                                header("location: index.php"); // Diubah ke halaman utama
                            }
                            exit;
                        } else {
                            // Password tidak valid
                            $login_err = "Password yang Anda masukkan tidak valid.";
                        }
                    }
                } else {
                    // Username tidak ditemukan
                    $login_err = "Tidak ada akun yang ditemukan dengan username tersebut.";
                }
            }
            $stmt->close();
        }
    }
    
    // Tutup koneksi
    $mysqli->close();
}

// Jika ada error, kembali ke halaman login dengan pesan error
if (!empty($login_err)) {
    // Menulis dan menutup sesi sebelum redirect
    session_write_close();
    header("location: login.php?error=" . urlencode($login_err));
    exit;
}
?>