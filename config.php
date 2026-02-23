<?php
/*
File Konfigurasi Database
*/

// Pengaturan koneksi database
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default username untuk XAMPP
define('DB_PASSWORD', ''); // Default password untuk XAMPP adalah kosong
define('DB_NAME', 'bdc_db'); // Nama database yang kita buat di database.sql

// Membuat koneksi ke database MySQL
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if($mysqli === false){
    die("ERROR: Tidak dapat terhubung ke database. " . $mysqli->connect_error);
}
?>