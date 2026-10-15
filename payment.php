<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
$query = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='{$_SESSION['user_id']}'");
$order = mysqli_fetch_assoc($query);

if (!$order) {
    header("Location: index.php");
    exit;
}

$message = '';

// Logika saat tombol konfirmasi / simulasi lunas ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['upload_bukti']) || isset($_POST['simulasi_lunas']))) {
    // Update status pesanan di database menjadi Lunas / Paid
    mysqli_query($conn, "UPDATE orders SET status='Paid' WHERE id='$order_id'");
    $message = "Pembayaran berhasil dikonfirmasi! Pesanan Anda sudah lunas.";
    $order['status'] = 'Paid';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran QRIS - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">

  <?php include 'navbar.php'; ?>

  <div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-slate-800 p-8 rounded-xl shadow-xl border border-slate-700 text-center">
      <h1 class="text-2xl font-bold mb-1 text-white">Pesanan Berhasil Dibuat!</h1>
      <p class="text-slate-400 text-sm mb-6">Silakan lakukan pembayaran QRIS di bawah ini untuk menyelesaikan transaksi.</p>

      <?php if ($message): ?>
        <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-300 p-3 rounded-lg mb-6 text-sm font-medium">
          <?= $message; ?>
        </div>
      <?php endif; ?>

      <!-- Kotak QRIS & Total -->
      <div class="bg-white text-slate-900 p-6 rounded-xl inline-block shadow-inner mb-6 w-full max-w-sm">
        <div class="flex justify-between items-center mb-4">
            <span class="font-black text-xl tracking-wider text-red-600">QRIS</span>
            <span class="text-xs font-semibold bg-slate-100 text-slate-700 px-2.5 py-1 rounded">AMR Production</span>
        </div>
        
        <!-- Gambar QR Code -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=00020101021126660014ID.CO.QRIS.WWW0118936009143000000000303UMI51440013ID.TO.QRIS.WWW" alt="QRIS" class="mx-auto w-48 h-48 object-contain my-2 border p-1 rounded">
        
        <div class="mt-4 text-xs text-slate-500 font-mono">Kode Transaksi: <br><strong class="text-slate-800">TRX-<?= $order['id']; ?>-<?= date('Ymd'); ?></strong></div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-xs text-slate-500 uppercase font-bold">Total Pembayaran:</p>
            <p class="text-2xl font-black text-emerald-600 mt-0.5">Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></p>
            <p class="text-[11px] text-slate-400 mt-1">(Scan menggunakan BCA, Mandiri, OVO, GoPay, DANA, dll.)</p>
        </div>
      </div>

      <!-- TOMBOL SIMULASI SUDAH BAYAR -->
      <?php if (($order['status'] ?? 'Pending') === 'Pending'): ?>
        <form method="POST" class="space-y-3">
          <button type="submit" name="upload_bukti" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-200 text-base">
            ✅ Saya Sudah Bayar (Simulasi Lunas)
          </button>
        </form>
      <?php else: ?>
        <div class="space-y-3">
            <div class="bg-emerald-800/40 text-emerald-300 py-2 rounded font-medium text-sm">Status: Lunas / Dibayar</div>
            <a href="pesanan_saya.php" class="block w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-3 rounded-lg transition">Lihat Pesanan Saya</a>
        </div>
      <?php endif; ?>

      <div class="mt-6">
          <a href="catalog.php" class="text-xs text-slate-400 hover:text-white underline">← Kembali ke Katalog</a>
      </div>

    </div>
  </div>

</body>
</html>