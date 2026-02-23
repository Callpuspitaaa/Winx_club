<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
require_once "../config.php";

// Tentukan tanggal. Default ke hari ini jika tidak ada tanggal yang dipilih.
$tanggal_pilihan = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil daftar semua anggota
$sql_users = "SELECT id, nama_lengkap, nis, kelas FROM users WHERE role = 'anggota' ORDER BY nama_lengkap ASC";
$result_users = $mysqli->query($sql_users);
$anggota = $result_users->fetch_all(MYSQLI_ASSOC);

// Ambil data absensi yang sudah ada untuk tanggal yang dipilih
$sql_absensi = "SELECT user_id, status FROM absensi WHERE tanggal = ?";
$absensi_hari_ini = [];
if($stmt_absensi = $mysqli->prepare($sql_absensi)){
    $stmt_absensi->bind_param("s", $tanggal_pilihan);
    $stmt_absensi->execute();
    $result_absensi = $stmt_absensi->get_result();
    while($row = $result_absensi->fetch_assoc()){
        $absensi_hari_ini[$row['user_id']] = $row['status'];
    }
    $stmt_absensi->close();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Absensi - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="content-area">
            <h2>Ambil Absensi</h2>
            <form method="GET" class="simple-form" style="margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="tanggal">Pilih Tanggal Pertemuan</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo $tanggal_pilihan; ?>" onchange="this.form.submit()">
                </div>
            </form>

            <form action="proses_absensi.php" method="POST">
                <input type="hidden" name="tanggal" value="<?php echo $tanggal_pilihan; ?>">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anggota as $member): ?>
                                <?php $status_sekarang = $absensi_hari_ini[$member['id']] ?? 'alpha'; // Default ke alpha ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($member['nis']); ?></td>
                                    <td><?php echo htmlspecialchars($member['kelas']); ?></td>
                                    <td><input type="radio" name="status[<?php echo $member['id']; ?>]" value="hadir" <?php echo ($status_sekarang == 'hadir') ? 'checked' : ''; ?>></td>
                                    <td><input type="radio" name="status[<?php echo $member['id']; ?>]" value="izin" <?php echo ($status_sekarang == 'izin') ? 'checked' : ''; ?>></td>
                                    <td><input type="radio" name="status[<?php echo $member['id']; ?>]" value="sakit" <?php echo ($status_sekarang == 'sakit') ? 'checked' : ''; ?>></td>
                                    <td><input type="radio" name="status[<?php echo $member['id']; ?>]" value="alpha" <?php echo ($status_sekarang == 'alpha') ? 'checked' : ''; ?>></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">Simpan Absensi</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>