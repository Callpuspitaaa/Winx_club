<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
require_once "../config.php";

// Tentukan periode (bulan & tahun)
$bulan_pilihan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Ambil daftar semua anggota
$sql_users = "SELECT id, nama_lengkap, nis, kelas FROM users WHERE role = 'anggota' ORDER BY nama_lengkap ASC";
$result_users = $mysqli->query($sql_users);
$anggota = $result_users->fetch_all(MYSQLI_ASSOC);

// Ambil data iuran yang sudah ada untuk periode yang dipilih
$sql_iuran = "SELECT user_id, status_pembayaran FROM iuran WHERE bulan = ? AND tahun = ?";
$status_iuran = [];
if($stmt_iuran = $mysqli->prepare($sql_iuran)){
    $stmt_iuran->bind_param("ii", $bulan_pilihan, $tahun_pilihan);
    $stmt_iuran->execute();
    $result_iuran = $stmt_iuran->get_result();
    while($row = $result_iuran->fetch_assoc()){
        $status_iuran[$row['user_id']] = $row['status_pembayaran'];
    }
    $stmt_iuran->close();
}

// Ambil jumlah iuran & hitung total
$jumlah_iuran = $mysqli->query("SELECT jumlah_iuran FROM pengaturan_iuran WHERE id = 1")->fetch_assoc()['jumlah_iuran'];
$jumlah_lunas = 0;
foreach($status_iuran as $status) {
    if ($status == 'lunas') {
        $jumlah_lunas++;
    }
}
$total_terkumpul = $jumlah_lunas * $jumlah_iuran;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Iuran - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="content-area">
            <h2>Data Iuran Anggota</h2>
            <form method="GET" class="simple-form" style="margin-bottom: 2rem;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="bulan">Pilih Bulan</label>
                        <select name="bulan" id="bulan" onchange="this.form.submit()">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($i == $bulan_pilihan) ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 10)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tahun">Pilih Tahun</label>
                        <input type="number" name="tahun" id="tahun" value="<?php echo $tahun_pilihan; ?>" onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <form action="proses_iuran.php" method="POST">
                <input type="hidden" name="bulan" value="<?php echo $bulan_pilihan; ?>">
                <input type="hidden" name="tahun" value="<?php echo $tahun_pilihan; ?>">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Status Pembayaran (Lunas)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggota as $member): ?>
                                <?php $status_sekarang = $status_iuran[$member['id']] ?? 'belum_lunas'; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($member['nis']); ?></td>
                                    <td><?php echo htmlspecialchars($member['kelas']); ?></td>
                                    <td><input type="checkbox" name="status_bayar[<?php echo $member['id']; ?>]" value="lunas" <?php echo ($status_sekarang == 'lunas') ? 'checked' : ''; ?>></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">Simpan Data Iuran</button>
            </form>
        </div>
        <div class="content-area" style="margin-top: 2rem;">
            <h2>Ringkasan Iuran (<?php echo date('F', mktime(0, 0, 0, $bulan_pilihan, 10)) . ' ' . $tahun_pilihan; ?>)</h2>
            <p>Jumlah Iuran per Anggota: <strong>Rp <?php echo number_format($jumlah_iuran, 0, ',', '.'); ?></strong></p>
            <p>Jumlah Anggota Lunas: <strong><?php echo $jumlah_lunas; ?> dari <?php echo count($anggota); ?> anggota</strong></p>
            <p>Total Terkumpul: <strong style="color: green;">Rp <?php echo number_format($total_terkumpul, 0, ',', '.'); ?></strong></p>
        </div>
    </main>
</div>
</body>
</html>