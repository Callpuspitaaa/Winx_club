<?php 
session_start();
require_once "config.php";

// Cek jika pengguna belum login, redirect ke halaman login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php?error=" . urlencode("Anda harus login untuk mendaftar ekskul."));
    exit;
}

$user_id = $_SESSION['id'];
$pendaftar_data = null;
$is_registered = false;

// Cek apakah pengguna sudah pernah mendaftar
$sql_check = "SELECT * FROM pendaftar WHERE user_id = ?";
if($stmt_check = $mysqli->prepare($sql_check)){
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    if($result->num_rows > 0){
        $is_registered = true;
        $pendaftar_data = $result->fetch_assoc();
    }
    $stmt_check->close();
}

// Ambil data pengguna dari tabel users untuk diisi ke form
$user_account_data = null;
$sql_user = "SELECT nama_lengkap, nis, kelas FROM users WHERE id = ?";
if($stmt_user = $mysqli->prepare($sql_user)){
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user_account_data = $user_result->fetch_assoc();
    $stmt_user->close();
}

// Ambil status pendaftaran (untuk buka/tutup form)
$sql_setting = "SELECT setting_value FROM settings WHERE setting_key = 'recruitment_open'";
$result_setting = $mysqli->query($sql_setting);
$recruitment_open = ($result_setting->fetch_assoc()['setting_value'] == 'true');

$error_msg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ekskul - BDC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-page-section { padding-top: 120px; padding-bottom: 6rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--light-gray); }
        .form-container { width: 100%; max-width: 600px; background: var(--white); padding: 2rem 3rem; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        .form-container h2 { font-size: 2rem; margin-bottom: 1rem; color: var(--dark-gray); }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: var(--font-main); font-size: 1rem; }
        .form-group input[readonly] { background-color: #eee; cursor: not-allowed; }
        .status-message { padding: 1.5rem; border-radius: 5px; text-align: center; margin-bottom: 1.5rem; border: 1px solid; }
        .status-pending { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        .status-diterima { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .status-ditolak { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .closed-message { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

    <main>
        <section class="form-page-section">
            <div class="form-container reveal">
                <h2>Formulir Pendaftaran Ekskul BDC</h2>

                <?php if ($is_registered): ?>
                    <?php 
                    $status = $pendaftar_data['status'];
                    $nama = htmlspecialchars($pendaftar_data['nama_lengkap']);
                    $message = '';
                    $class = '';
                    switch ($status) {
                        case 'pending':
                            $message = "Pendaftaran Anda atas nama <strong>{$nama}</strong> sedang diproses. Silakan tunggu pengumuman selanjutnya.";
                            $class = 'status-pending';
                            break;
                        case 'diterima':
                            $message = "Selamat! Pendaftaran Anda atas nama <strong>{$nama}</strong> telah diterima. Anda sekarang resmi menjadi anggota BDC. Silakan tunggu konfirmasi lebih lanjut melalui WhatsApp atau pantau informasi untuk dimasukkan ke grup WhatsApp khusus ekstra dance.";
                            $class = 'status-diterima';
                            break;
                        case 'ditolak':
                            $message = "Mohon maaf, pendaftaran Anda atas nama <strong>{$nama}</strong> belum dapat kami terima saat ini. Terima kasih atas partisipasi Anda.";
                            $class = 'status-ditolak';
                            break;
                    }
                    ?>
                    <div class="status-message <?php echo $class; ?>">
                        <p><?php echo $message; ?></p>
                    </div>
                    <a href="index.php" class="btn">Kembali ke Halaman Utama</a>
                <?php elseif (!$recruitment_open): ?>
                    <div class="closed-message">
                        <p>Mohon maaf, pendaftaran untuk periode ini sudah ditutup. Tunggu informasi selanjutnya.</p>
                    </div>
                <?php else: ?>
                    <p style="margin-bottom: 2rem;">Data di bawah ini diambil dari akun Anda. Pastikan data NIS dan Kelas sudah terisi, lalu lengkapi sisanya.</p>
                    <form action="proses_pendaftaran.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user_account_data['nama_lengkap']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nis">NIS (Nomor Induk Siswa)</label>
                            <input type="text" id="nis" name="nis" value="<?php echo htmlspecialchars($user_account_data['nis']); ?>" <?php echo !empty($user_account_data['nis']) ? 'readonly' : 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <input type="text" id="kelas" name="kelas" value="<?php echo htmlspecialchars($user_account_data['kelas']); ?>" <?php echo !empty($user_account_data['kelas']) ? 'readonly' : 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">Nomor HP/WhatsApp</label>
                            <input type="text" id="no_hp" name="no_hp" required>
                        </div>
                        <div class="form-group">
                            <label for="alasan_bergabung">Alasan Bergabung</label>
                            <textarea id="alasan_bergabung" name="alasan_bergabung" rows="4" required></textarea>
                        </div>
                        <div class="form-group" style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                            <label for="video_dance" style="font-size: 1.2rem; color: var(--primary-color); margin-bottom: 10px;">Unggah Video Dance</label>
                            <p style="font-size: 0.9rem; color: #666; margin-top: 0; margin-bottom: 15px;">
                                Tunjukan kemampuanmu bahwa kamu berhak masuk ekstra dance ini!<br>
                                <strong>Syarat:</strong> Video dance bebas (genre apa saja), durasi minimal 1 menit.
                            </p>
                            <input type="file" id="video_dance" name="video_dance" accept="video/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Kirim Pendaftaran</button>
                    </form>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>