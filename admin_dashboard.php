<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$orders = mysqli_query($conn, "SELECT orders.*, users.nama FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

  <?php include 'navbar.php'; ?>

  <div class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-slate-900">Dashboard Admin (Laporan Sewa)</h1>
      <a href="export_excel.php" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">Export Ke Excel</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-100 border-b text-slate-700 text-sm">
            <th class="p-4">ID</th>
            <th class="p-4">Nama penyewa</th>
            <th class="p-4">Tgl Sewa</th>
            <th class="p-4">Tgl Kembali</th>
            <th class="p-4">Total</th>
            <th class="p-4">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($orders)): ?>
            <tr class="border-b hover:bg-slate-50">
              <td class="p-4 font-mono font-bold">#<?= $row['id']; ?></td>
              <td class="p-4 font-medium"><?= htmlspecialchars($row['nama']); ?></td>
              <td class="p-4"><?= $row['tgl_sewa']; ?></td>
              <td class="p-4"><?= $row['tgl_kembali']; ?></td>
              <td class="p-4 font-semibold text-indigo-600">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
              <td class="p-4">
                <span class="px-2.5 py-1 text-xs font-bold rounded-full <?= $row['status'] == 'Paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                  <?= $row['status']; ?>
                </span>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>