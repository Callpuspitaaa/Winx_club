<?php 
$error_msg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$success_msg = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - BDC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-page-section { padding-top: 120px; padding-bottom: 6rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--light-gray); }
        .form-container { width: 100%; max-width: 500px; background: var(--white); padding: 2rem 3rem; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-container h2 { text-align: center; font-size: 2rem; margin-bottom: 1rem; color: var(--dark-gray); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-family: var(--font-main); font-size: 1rem; }
        .message { padding: 1rem; border-radius: 5px; text-align: center; margin-bottom: 1.5rem; border: 1px solid; }
        .error-message { background-color: rgba(255, 0, 110, 0.1); color: var(--secondary-pink); border-color: var(--secondary-pink); }
        .success-message { background-color: rgba(40, 167, 69, 0.1); color: #155724; border-color: #c3e6cb; }
        .form-container p.info { text-align: center; margin-bottom: 2rem; color: #666; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <section class="form-page-section">
            <div class="form-container reveal">
                <h2>Buat Akun Baru</h2>
                <p class="info">Gunakan email sekolah yang valid dari jurusan Anda (contoh: siswa@pplg.smkn1bawang.sch.id) untuk mendaftar.</p>
                <?php if(!empty($error_msg)): ?><div class="message error-message"><p><?php echo $error_msg; ?></p></div><?php endif; ?>
                <?php if(!empty($success_msg)): ?><div class="message success-message"><p><?php echo $success_msg; ?></p></div><?php endif; ?>
                <form action="proses_registrasi_akun.php" method="POST">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="nis">NIS (Nomor Induk Siswa)</label>
                        <input type="text" id="nis" name="nis" required>
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" id="kelas" name="kelas" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Sekolah</label>
                        <input type="email" id="email" name="email" required pattern=".+@(pplg|mplb|akl|fs|te|pm|ap|tjkt)\.smkn1bawang\.sch\.id" title="Hanya email sekolah dari jurusan yang valid yang diizinkan.">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Registrasi</button>
                </form>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>