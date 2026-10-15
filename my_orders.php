<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data orders berdasarkan user_id
$query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Jika kolom user_id dinamai lain (id_user / id_pelanggan)
if (!$result) {
    $query = "SELECT * FROM orders WHERE id_user = '$user_id' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pesanan Saya - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

  <?php include 'navbar.php'; ?>

  <div class="max-w-5xl mx-auto py-10 px-4 flex-grow w-full">
    <h1 class="text-2xl font-bold text-white mb-6">Riwayat Pesanan Saya</h1>

    <div class="bg-slate-950 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-900 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-800">
              <th class="p-4">ID Order</th>
              <th class="p-4">Tgl Sewa</th>
              <th class="p-4">Tgl Kembali</th>
              <th class="p-4">Total Harga</th>
              <th class="p-4">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800 text-sm">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php 
                  // Ambil nilai kolom secara dinamis dengan pencocokan nama alternatif
                  $id_order = $row['id'] ?? $row['kode_order'] ?? $row['id_order'] ?? '-';
                  $tgl_sewa = $row['tgl_sewa'] ?? $row['tanggal_sewa'] ?? $row['tgl_mulai'] ?? $row['start_date'] ?? '-';
                  $tgl_kembali = $row['tgl_kembali'] ?? $row['tanggal_kembali'] ?? $row['tgl_selesai'] ?? $row['end_date'] ?? '-';
                  $total_harga = $row['total_harga'] ?? $row['total'] ?? $row['grand_total'] ?? $row['harga_total'] ?? 0;
                  $status = $row['status'] ?? $row['status_pesanan'] ?? $row['status_order'] ?? $row['status_pembayaran'] ?? 'pending';

                  // Tentukan warna status
                  $status_badge = 'bg-amber-500/20 text-amber-400 border-amber-500/30';
                  if (in_array(strtolower($status), ['lunas', 'paid', 'success', 'disetujui'])) {
                      $status_badge = 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
                  } elseif (in_array(strtolower($status), ['batal', 'cancelled', 'rejected', 'gagal'])) {
                      $status_badge = 'bg-red-500/20 text-red-400 border-red-500/30';
                  }
                ?>
                <tr class="hover:bg-slate-900/50 transition">
                  <td class="p-4 font-mono text-indigo-400 font-semibold">#<?= htmlspecialchars($id_order); ?></td>
                  <td class="p-4 text-slate-300"><?= htmlspecialchars($tgl_sewa); ?></td>
                  <td class="p-4 text-slate-300"><?= htmlspecialchars($tgl_kembali); ?></td>
                  <td class="p-4 font-bold text-white">Rp <?= number_format($total_harga, 0, ',', '.'); ?></td>
                  <td class="p-4">
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full border <?= $status_badge; ?>">
                      <?= ucfirst(htmlspecialchars($status)); ?>
                    </span>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="p-8 text-center text-slate-500">
                  Belum ada riwayat pesanan.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>
</html>