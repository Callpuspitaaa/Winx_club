<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
require_once "../config.php";

// --- PENGHITUNGAN TOTAL KAS KESELURUHAN (VERSI AMAN) ---
$grand_total = 0;
$total_pemasukan_iuran = 0;
$total_pemasukan_umum = 0;
$total_pengeluaran_umum = 0;

// 1. Ambil jumlah iuran per anggota
$jumlah_iuran = 0;
$jumlah_iuran_result = $mysqli->query("SELECT jumlah_iuran FROM pengaturan_iuran WHERE id = 1");
if ($jumlah_iuran_result && $jumlah_iuran_result->num_rows > 0) {
    $jumlah_iuran = $jumlah_iuran_result->fetch_assoc()['jumlah_iuran'] ?? 0;
}

// 2. Hitung total dari semua iuran yang lunas
$result_total_iuran = $mysqli->query("SELECT COUNT(*) as total_lunas FROM iuran WHERE status_pembayaran = 'lunas'");
if ($result_total_iuran && $result_total_iuran->num_rows > 0) {
    $total_iuran_lunas = $result_total_iuran->fetch_assoc()['total_lunas'];
    $total_pemasukan_iuran = $total_iuran_lunas * $jumlah_iuran;
}

// 3. Hitung total dari tabel kas (transaksi umum)
$result_kas_umum = $mysqli->query("SELECT SUM(pemasukan) as total_pemasukan, SUM(pengeluaran) as total_pengeluaran FROM kas");
if ($result_kas_umum && $result_kas_umum->num_rows > 0) {
    $data_kas_umum = $result_kas_umum->fetch_assoc();
    $total_pemasukan_umum = $data_kas_umum['total_pemasukan'] ?? 0;
    $total_pengeluaran_umum = $data_kas_umum['total_pengeluaran'] ?? 0;
}

// 4. Hitung Grand Total
$grand_total = ($total_pemasukan_iuran + $total_pemasukan_umum) - $total_pengeluaran_umum;


// --- LOGIKA UNTUK TAB --- 
$active_tab = isset($_GET['tab']) && $_GET['tab'] == 'transaksi' ? 'transaksi' : 'iuran';

// Variabel-variabel untuk tab iuran (perlu di-define di sini agar bisa diakses oleh iuran_content.php)
if ($active_tab == 'iuran') {
    if (isset($_GET['tanggal'])) {
        $tanggal_pilihan = $_GET['tanggal'];
    } else {
        if (date('N') == 3) { // Rabu
            $tanggal_pilihan = date('Y-m-d');
        } else {
            $tanggal_pilihan = date('Y-m-d', strtotime('last wednesday'));
        }
    }
    $sql_users = "SELECT id, nama_lengkap, nis, kelas FROM users WHERE role = 'anggota' ORDER BY nama_lengkap ASC";
    $result_users = $mysqli->query($sql_users);
    $anggota = $result_users ? $result_users->fetch_all(MYSQLI_ASSOC) : [];

    $status_iuran = [];
    $sql_iuran = "SELECT user_id, status_pembayaran FROM iuran WHERE tanggal_pertemuan = ?";
    if($stmt_iuran = $mysqli->prepare($sql_iuran)){
        $stmt_iuran->bind_param("s", $tanggal_pilihan);
        $stmt_iuran->execute();
        $result_iuran = $stmt_iuran->get_result();
        while($row = $result_iuran->fetch_assoc()){
            $status_iuran[$row['user_id']] = $row['status_pembayaran'];
        }
        $stmt_iuran->close();
    }
    $jumlah_lunas = 0;
    foreach($status_iuran as $status) {
        if ($status == 'lunas') $jumlah_lunas++;
    }
    $total_terkumpul = $jumlah_lunas * $jumlah_iuran;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Keuangan - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .grand-total-card { 
            background-color: #6f42c1; /* Warna ungu langsung */
            color: white; 
            text-align: center; 
            padding: 1.5rem; 
            border-radius: 8px; 
            margin-bottom: 2rem; 
        }
        .grand-total-card h2 { margin: 0; font-size: 1.2rem; font-weight: 400; } 
        .grand-total-card p { margin: 0.5rem 0 0; font-size: 2.5rem; font-weight: 700; }
        .tabs { display: flex; border-bottom: 2px solid #eee; margin-bottom: 2rem; }
        .tabs a { padding: 1rem 1.5rem; text-decoration: none; color: #888; font-weight: 600; border-bottom: 2px solid transparent; }
        .tabs a.active { color: #6f42c1; border-bottom-color: #6f42c1; }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="content-area">
            <div class="grand-total-card">
                <h2>Total Kas Keseluruhan</h2>
                <p>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></p>
            </div>

            <div class="tabs">
                <a href="kas.php?tab=iuran" class="<?php echo ($active_tab == 'iuran') ? 'active' : ''; ?>">Iuran Anggota</a>
                <a href="kas.php?tab=transaksi" class="<?php echo ($active_tab == 'transaksi') ? 'active' : ''; ?>">Riwayat Transaksi</a>
            </div>

            <div class="tab-content">
                <?php 
                if ($active_tab == 'iuran') {
                    include 'iuran_content.php';
                } else {
                    include 'transaksi_content.php';
                }
                ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>