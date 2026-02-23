<?php
// Mulai sesi
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Hancurkan sesi
session_destroy();

// Mengarahkan kembali ke halaman utama setelah logout
header("location: index.php");
exit;
?>