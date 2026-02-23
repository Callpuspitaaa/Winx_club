<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
require_once "../config.php";

// Ambil data galeri
$sql = "SELECT id, judul_gambar, path_gambar FROM galeri ORDER BY uploaded_at DESC";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Galeri - BDC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="content-area">
            <h2>Tambah Foto Baru</h2>
            <form action="proses_galeri.php" method="POST" enctype="multipart/form-data" class="simple-form">
                <div class="form-group">
                    <label for="judul">Judul Foto</label>
                    <input type="text" id="judul" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="foto">Pilih File Gambar (JPG, PNG, JPEG)</label>
                    <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload Foto</button>
            </form>
        </div>
        <div class="content-area" style="margin-top: 2rem;">
            <h2>Galeri Tersimpan</h2>
            <div class="gallery-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                 <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="gallery-item">
                            <img src="../<?php echo htmlspecialchars($row['path_gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul_gambar']); ?>">
                            <!-- Opsi hapus bisa ditambahkan di sini -->
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Belum ada gambar di galeri.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>