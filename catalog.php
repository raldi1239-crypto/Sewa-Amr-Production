<?php
require_once 'config.php';

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }

    header("Location: catalog.php");
    exit;
}

// Fungsi otomatis untuk mengambil gambar dari internet berdasarkan nama produk
function getAutoImage($nama_produk) {
    $keywords = [
        'sound'    => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=600&q=80',
        'mic'      => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=600&q=80',
        'panggung' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=600&q=80',
        'lighting' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=600&q=80',
        'lampu'    => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=600&q=80',
        'speaker'  => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=600&q=80'
    ];

    $nama = strtolower($nama_produk);
    foreach ($keywords as $key => $url) {
        if (strpos($nama, $key) !== false) {
            return $url;
        }
    }

    return 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=600&q=80';
}

$products = mysqli_query($conn, "SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Katalog Peralatan - AMR Production</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

  <?php include 'navbar.php'; ?>

  <div class="max-w-7xl mx-auto py-12 px-4 flex-grow w-full">
    <div class="mb-10 text-center md:text-left">
      <h1 class="text-3xl md:text-4xl font-extrabold text-white">Katalog Peralatan Event</h1>
      <p class="text-slate-400 mt-2 text-sm">Pilih peralatan kualitas terbaik untuk kebutuhan event kamu.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php while ($row = mysqli_fetch_assoc($products)): ?>
        <?php 
          // Ambil gambar otomatis dari internet
          $image_url = getAutoImage($row['nama_produk']);
        ?>
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden flex flex-col justify-between hover:border-indigo-500/50 transition duration-300 shadow-lg">
          
          <div>
            <!-- Gambar Produk Otomatis -->
            <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
              <img src="<?= $image_url; ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-300">
              <span class="absolute top-3 left-3 text-[10px] font-bold bg-indigo-600/90 text-white px-2.5 py-1 rounded-full uppercase tracking-wider backdrop-blur-sm">
                <?= htmlspecialchars($row['kategori']); ?>
              </span>
            </div>

            <!-- Detail Produk -->
            <div class="p-5">
              <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($row['nama_produk']); ?></h2>
              <p class="text-slate-400 text-xs mt-2 line-clamp-2 leading-relaxed"><?= htmlspecialchars($row['deskripsi']); ?></p>
              
              <div class="mt-4 flex justify-between items-end">
                <div>
                  <p class="text-[10px] text-slate-500 uppercase tracking-wider">Harga Sewa</p>
                  <p class="text-indigo-400 font-extrabold text-lg">
                    Rp <?= number_format($row['harga_per_hari'], 0, ',', '.'); ?>
                    <span class="text-[10px] text-slate-500 font-normal">/ hari</span>
                  </p>
                </div>
                <span class="text-xs text-slate-400 bg-slate-900 px-2.5 py-1 rounded border border-slate-800">
                  Stok: <strong class="text-white"><?= $row['stok']; ?></strong>
                </span>
              </div>
            </div>
          </div>

          <!-- Form Tambah ke Keranjang -->
          <div class="p-5 bg-slate-900/60 border-t border-slate-800/80">
            <form method="POST" class="flex items-center gap-2">
              <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
              <input type="number" name="quantity" value="1" min="1" max="<?= $row['stok']; ?>" class="w-14 bg-slate-950 border border-slate-700 text-white text-center rounded-lg py-1.5 text-sm focus:outline-none focus:border-indigo-500">
              <button type="submit" name="add_to_cart" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-1.5 px-3 rounded-lg text-sm transition flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/20">
                <i class="fa-solid fa-cart-plus text-xs"></i> Sewa
              </button>
            </form>
          </div>

        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <footer class="bg-slate-950 border-t border-slate-800 text-slate-500 text-center py-4 text-xs mt-auto">
    &copy; <?= date('Y'); ?> AMR Production. All rights reserved.
  </footer>

</body>
</html>