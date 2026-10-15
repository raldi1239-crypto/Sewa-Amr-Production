<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit("Akses Ditolak");
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Penyewaan_AMR.xls");

$orders = mysqli_query($conn, "SELECT orders.*, users.nama FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.id DESC");
?>
<table border="1">
  <thead>
    <tr>
      <th>ID Order</th>
      <th>Nama Penyewa</th>
      <th>Tanggal Sewa</th>
      <th>Tanggal Kembali</th>
      <th>Total Harga</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($orders)): ?>
      <tr>
        <td>#<?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['nama']); ?></td>
        <td><?= $row['tgl_sewa']; ?></td>
        <td><?= $row['tgl_kembali']; ?></td>
        <td><?= $row['total_harga']; ?></td>
        <td><?= $row['status']; ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>