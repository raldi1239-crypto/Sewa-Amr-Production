<?php
session_start();
require_once 'config.php';
$db_conn = isset($conn) ? $conn : (isset($mysqli) ? $mysqli : null);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identity = trim($_POST['username'] ?? ''); // Bisa diisi email atau username
    $password = trim($_POST['password'] ?? '');

    if (!empty($identity) && !empty($password)) {
        // Cek data berdasarkan email atau username
        $stmt = mysqli_prepare($db_conn, "SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $identity, $identity);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verifikasi password (mendukung password biasa atau hash)
            if ($password === $row['password'] || password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'] ?? $row['email'];
                $_SESSION['role'] = $row['role'];

                // Arahkan otomatis berdasarkan role
                if (strtolower($row['role']) === 'admin') {
                    header("Location: admin_orders.php");
                    exit;
                } else {
                    header("Location: catalog.php");
                    exit;
                }
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username atau Email tidak ditemukan!";
        }
    } else {
        $error = "Mohon isi kolom login dan password!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - AMR Production</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 360px; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 14px; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-login { width: 100%; background: #2c3e50; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-login:hover { background: #1a252f; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 13px; text-align: center; }
        .hint { font-size: 12px; color: #666; margin-top: 15px; text-align: center; border-top: 1px solid #eee; padding-top: 10px; line-height: 1.5; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>AMR Production</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username / Email</label>
            <input type="text" name="username" required autocomplete="off" placeholder="Masukkan username atau email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn-login">Masuk</button>
    </form>

    <div class="hint">
        <b>Admin:</b> admin@amrproduction.com / admin123<br>
        <b>User:</b> user@gmail.com / (password bawaan)
    </div>
</div>

</body>
</html>