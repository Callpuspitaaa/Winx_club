<?php 
// Mulai sesi untuk memeriksa status login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke dashboard yang sesuai
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if($_SESSION["role"] == 'admin'){
        header("location: admin/dashboard.php");
    } else {
        header("location: anggota/dashboard.php");
    }
    exit;
}

$error_msg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BDC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-page-section {
            padding-top: 120px; /* space for fixed header */
            padding-bottom: 6rem;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(131, 56, 236, 0.05), rgba(255, 0, 110, 0.05));
        }
        .form-container {
            width: 100%;
            max-width: 450px;
            background: var(--white);
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .form-container img {
            height: 60px;
            margin-bottom: 1rem;
        }
        .form-container h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--dark-blue);
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: var(--font-main);
            font-size: 1rem;
        }
        .error-message {
            background-color: rgba(255, 0, 110, 0.1);
            color: var(--secondary-pink);
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 1.5rem;
            border: 1px solid var(--secondary-pink);
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main>
        <section class="form-page-section">
            <div class="form-container reveal">
                <img src="logo.png" alt="BDC Logo">
                <h2>Login Anggota & Admin</h2>
                <?php if(!empty($error_msg)): ?>
                    <div class="error-message">
                        <p><?php echo $error_msg; ?></p>
                    </div>
                <?php endif; ?>
                <form action="proses_login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </form>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>