<?php
require_once 'config.php';

// Hapus barang dari keranjang
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Update jumlah barang
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = (int)$qty;
        }
    }
    header("Location: cart.php");
    exit;
}

$cart_items = [];
$total_keseluruhan = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($query)) {
        $row['qty'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['harga_per_hari'] * $row['qty'];
        $total_keseluruhan += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keranjang Belanja - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

  <?php include 'navbar.php'; ?>

  <div class="max-w-5xl mx-auto py-10 px-4 flex-grow w-full">
    <h1 class="text-3xl font-bold mb-6 text-slate-900">Keranjang Sewa</h1>

    <?php if (empty($cart_items)): ?>
      <div class="bg-white p-8 rounded-lg shadow-sm border text-center">
        <p class="text-slate-600 mb-4">Keranjang Anda masih kosong.</p>
        <a href="catalog.php" class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Lihat Katalog</a>
      </div>
    <?php else: ?>
      <form method="POST" action="cart.php" class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100 border-b text-slate-700 text-sm">
              <th class="p-4">Produk</th>
              <th class="p-4">Harga / Hari</th>
              <th class="p-4">Jumlah</th>
              <th class="p-4">Subtotal</th>
              <th class="p-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_items as $item): ?>
              <tr class="border-b hover:bg-slate-50">
                <td class="p-4 font-semibold text-slate-800"><?= htmlspecialchars($item['nama_produk']); ?></td>
                <td class="p-4">Rp <?= number_format($item['harga_per_hari'], 0, ',', '.'); ?></td>
                <td class="p-4">
                  <input type="number" name="quantity[<?= $item['id']; ?>]" value="<?= $item['qty']; ?>" min="1" max="<?= $item['stok']; ?>" class="w-16 border rounded p-1 text-center">
                </td>
                <td class="p-4 font-semibold text-indigo-600">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                <td class="p-4 text-center">
                  <a href="cart.php?action=delete&id=<?= $item['id']; ?>" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="p-6 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
          <button type="submit" name="update_cart" class="border border-slate-300 text-slate-700 bg-white hover:bg-slate-100 px-4 py-2 rounded text-sm transition">Update Keranjang</button>
          
          <div class="text-right">
            <p class="text-slate-600 text-sm">Total Estimasi Peralatan:</p>
            <p class="text-2xl font-extrabold text-indigo-600">Rp <?= number_format($total_keseluruhan, 0, ',', '.'); ?></p>
            <a href="checkout.php" class="mt-3 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-2.5 rounded-lg transition shadow-md">Lanjut ke Checkout &rarr;</a>
          </div>
        </div>
      </form>
    <?php endif; ?>
  </div>

</body>
</html>