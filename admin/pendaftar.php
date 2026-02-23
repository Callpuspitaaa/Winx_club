<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

require_once "../config.php";

// Ambil data pendaftar yang masih pending
$sql = "SELECT * FROM pendaftar WHERE status = 'pending' ORDER BY tanggal_daftar ASC";
$result = $mysqli->query($sql);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pendaftar Baru - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .alasan-column {
            max-width: 300px; /* Batasi lebar kolom alasan */
            white-space: normal; /* Izinkan word wrap */
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        
        <div class="content-area">
            <?php 
            if(isset($_SESSION['new_user_info'])){
                echo '<p style="background-color: #e7f3fe; color: #31708f; padding: 15px; border-radius: 5px; margin-bottom: 1rem;">' . $_SESSION['new_user_info'] . '</p>';
                unset($_SESSION['new_user_info']); // Hapus session setelah ditampilkan
            }
            ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>No. HP/WhatsApp</th>
                            <th>Video Dance</th>
                            <th>Alasan Bergabung</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                    <td>
                                        <?php if (!empty($row['video_path'])): ?>
                                            <a href="../<?php echo htmlspecialchars($row['video_path']); ?>" target="_blank" class="action-btn">Lihat Video</a>
                                        <?php else: ?>
                                            <span>Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="alasan-column"><?php echo htmlspecialchars($row['alasan_bergabung']); ?></td>
                                    <td><?php echo date("d M Y", strtotime($row['tanggal_daftar'])); ?></td>
                                    <td>
                                        <a href="proses_persetujuan.php?id=<?php echo $row['id']; ?>&aksi=terima" class="action-btn btn-terima">Terima</a>
                                        <a href="proses_persetujuan.php?id=<?php echo $row['id']; ?>&aksi=tolak" class="action-btn btn-tolak">Tolak</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">Tidak ada pendaftar baru.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>