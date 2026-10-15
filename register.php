<?php
require_once 'config.php';

$message = '';
$error   = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Email sudah terdaftar!';
    } else {
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'user')";
        if (mysqli_query($conn, $query)) {
            $message = 'Pendaftaran berhasil! Silakan <a href="login.php" class="underline font-bold">Login</a>.';
        } else {
            $error = 'Terjadi kesalahan sistem.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akun - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-slate-800 mb-6">Daftar Akun Baru</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?= $error; ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"><?= $message; ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
        <input type="text" name="nama" required class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input type="email" name="email" required class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input type="password" name="password" required class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
      </div>
      <button type="submit" name="register" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-md transition text-sm">Daftar</button>
    </form>

    <p class="text-center text-sm text-slate-600 mt-4">
      Sudah punya akun? <a href="login.php" class="text-indigo-600 hover:underline">Login di sini</a>
    </p>
  </div>

</body>
</html>