<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error_db = '';

// PROSES SIMPAN FORM & BUAT PESANAN BARU
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_checkout'])) {
    $nama_pemesan = mysqli_real_escape_string($conn, $_POST['nama_pemesan'] ?? '');
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp'] ?? '');
    $tgl_mulai = mysqli_real_escape_string($conn, $_POST['tanggal_mulai'] ?? '');
    $tgl_selesai = mysqli_real_escape_string($conn, $_POST['tanggal_selesai'] ?? '');
    $lokasi_acara = mysqli_real_escape_string($conn, $_POST['lokasi_acara'] ?? '');

    if (strtotime($tgl_selesai) < strtotime($tgl_mulai)) {
        $error_db = "Error: Tanggal selesai tidak boleh lebih awal dari tanggal mulai sewa!";
    } elseif (empty($_SESSION['cart'])) {
        $error_db = "Error: Keranjang belanja Anda kosong!";
    } else {
        // Hitung durasi dan total harga dari keranjang
        $diff = strtotime($tgl_selesai) - strtotime($tgl_mulai);
        $jumlah_hari = max(1, floor($diff / (60 * 60 * 24)) + 1);

        $total_keseluruhan = 0;
        $ids = implode(',', array_keys($_SESSION['cart']));
        $query_prod = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
        while ($row = mysqli_fetch_assoc($query_prod)) {
            $harga = floatval($row['price'] ?? $row['harga_per_hari'] ?? 0);
            $qty = intval($_SESSION['cart'][$row['id']]);
            $total_keseluruhan += ($harga * $qty);
        }
        $total_akhir = $total_keseluruhan * $jumlah_hari;
        $rent_range = $tgl_mulai . " s/d " . $tgl_selesai . " ($jumlah_hari Hari)";
        $kode_order = 'AMR-' . date('ymd') . '-' . rand(100, 999);

        // Ambil struktur kolom tabel orders untuk mencegah error missing field
        $table_fields = [];
        $res_cols = mysqli_query($conn, "SHOW COLUMNS FROM orders");
        while ($col_row = mysqli_fetch_assoc($res_cols)) {
            $table_fields[$col_row['Field']] = $col_row;
        }

        // Siapkan data insert dinamis berdasarkan kolom yang benar-benar ada di database Anda
        $insert_data = [];
        
        if (isset($table_fields['kode_order'])) $insert_data['kode_order'] = $kode_order;
        if (isset($table_fields['user_id'])) $insert_data['user_id'] = $user_id;
        
        // Nama pemesan / customer
        if (isset($table_fields['nama_pemesan'])) $insert_data['nama_pemesan'] = $nama_pemesan;
        if (isset($table_fields['customer_name'])) $insert_data['customer_name'] = $nama_pemesan;
        
        // No Telepon / WhatsApp
        if (isset($table_fields['whatsapp'])) $insert_data['whatsapp'] = $whatsapp;
        if (isset($table_fields['phone'])) $insert_data['phone'] = $whatsapp;
        
        // Tanggal
        if (isset($table_fields['tanggal_mulai'])) $insert_data['tanggal_mulai'] = $tgl_mulai;
        if (isset($table_fields['tanggal_selesai'])) $insert_data['tanggal_selesai'] = $tgl_selesai;
        if (isset($table_fields['tanggal_sewa'])) $insert_data['tanggal_sewa'] = $rent_range;
        if (isset($table_fields['rental_date'])) $insert_data['rental_date'] = $rent_range;
        
        // Lokasi
        if (isset($table_fields['lokasi_acara'])) $insert_data['lokasi_acara'] = $lokasi_acara;
        
        // Durasi hari jika ada kolomnya
        if (isset($table_fields['durasi_hari'])) $insert_data['durasi_hari'] = $jumlah_hari;
        
        // Total harga
        if (isset($table_fields['total_harga'])) $insert_data['total_harga'] = $total_akhir;
        if (isset($table_fields['total_price'])) $insert_data['total_price'] = $total_akhir;
        
        // Status & Metode Pembayaran QRIS
        if (isset($table_fields['status'])) $insert_data['status'] = 'Pending';
        if (isset($table_fields['metode_pembayaran'])) $insert_data['metode_pembayaran'] = 'QRIS';

        // Bangun kueri INSERT secara dinamis
        $columns_sql = implode(", ", array_keys($insert_data));
        $values_sql = "'" . implode("', '", array_values($insert_data)) . "'";

        $insert_sql = "INSERT INTO orders ($columns_sql) VALUES ($values_sql)";
        
        if (mysqli_query($conn, $insert_sql)) {
            $new_order_id = mysqli_insert_id($conn);
            unset($_SESSION['cart']); // Kosongkan keranjang
            header("Location: checkout.php?order_id=$new_order_id");
            exit;
        } else {
            $error_db = "MySQL Error: " . mysqli_error($conn);
        }
    }
}

// Cek apakah halaman dipanggil dengan order_id
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = null;
if ($order_id > 0) {
    $q_ord = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id");
    $order = mysqli_fetch_assoc($q_ord);
}

// Proses Simulasi Lunas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simulasi_lunas']) && $order) {
    $current_id = intval($order['id']);
    mysqli_query($conn, "UPDATE orders SET status='Paid' WHERE id = $current_id");
    $message = "Pembayaran Berhasil Disimulasikan Lunas!";
    $order['status'] = 'Paid';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout & Pembayaran - AMR Production</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen py-10 px-4">

  <div class="max-w-xl mx-auto">
    <div class="bg-slate-800 p-8 rounded-xl shadow-xl border border-slate-700">
      
      <?php if (!empty($message)): ?>
        <div class="bg-emerald-600 text-white p-3 rounded-lg mb-6 text-sm font-bold text-center">
            <?= $message; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_db)): ?>
        <div class="bg-red-600 text-white p-3 rounded-lg mb-6 text-sm font-bold text-center">
            <?= $error_db; ?>
        </div>
      <?php endif; ?>

      <!-- KONDISI 1: JIKA BELUM ADA ORDER_ID (MUNCUL FORM ISI DATA DULU) -->
      <?php if (!$order): ?>
        <h1 class="text-2xl font-bold mb-1 text-white text-center">Form Detail Sewa & Acara</h1>
        <p class="text-slate-400 text-sm mb-6 text-center">Lengkapi data di bawah ini sebelum melanjutkan ke pembayaran QRIS.</p>

        <form method="POST" class="space-y-4 mb-6">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pemesan:</label>
                <input type="text" name="nama_pemesan" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 text-white text-sm" placeholder="Nama lengkap...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">No WhatsApp:</label>
                <input type="text" name="whatsapp" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 text-white text-sm" placeholder="08123456789">
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Dari Tanggal (Mulai):</label>
                    <input type="date" name="tanggal_mulai" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Sampai Tanggal (Selesai):</label>
                    <input type="date" name="tanggal_selesai" required class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Lokasi / Dimana Alat Dipakai:</label>
                <textarea name="lokasi_acara" required rows="2" class="w-full p-2.5 rounded bg-slate-700 border border-slate-600 text-white text-sm" placeholder="Gedung / Alamat acara..."></textarea>
            </div>
            
            <button type="submit" name="proses_checkout" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-lg transition text-sm cursor-pointer shadow-lg">
                Lanjut ke Pembayaran QRIS →
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="cart.php" class="text-xs text-slate-400 hover:text-white underline">← Kembali ke Keranjang</a>
        </div>

      <!-- KONDISI 2: JIKA SUDAH ISI FORM (MUNCUL QRIS) -->
      <?php else: ?>
        <h1 class="text-2xl font-bold mb-1 text-white text-center">Pembayaran QRIS</h1>
        <p class="text-slate-400 text-sm mb-6 text-center">ID Pesanan: #<?= $order['id']; ?> | Kode: <?= htmlspecialchars($order['kode_order'] ?? '-'); ?></p>

        <div class="text-center">
          <div class="bg-slate-700/50 p-3 rounded-lg mb-4 text-xs text-left space-y-1 border border-slate-600">
             <p><strong class="text-indigo-300">Pemesan:</strong> <?= htmlspecialchars($order['nama_pemesan'] ?? $order['customer_name'] ?? '-'); ?></p>
             <p><strong class="text-indigo-300">WhatsApp:</strong> <?= htmlspecialchars($order['whatsapp'] ?? $order['phone'] ?? '-'); ?></p>
             <p><strong class="text-indigo-300">Durasi Sewa:</strong> <?= htmlspecialchars($order['tanggal_sewa'] ?? $order['rental_date'] ?? '-'); ?></p>
             <p><strong class="text-indigo-300">Lokasi:</strong> <?= htmlspecialchars($order['lokasi_acara'] ?? '-'); ?></p>
          </div>

          <div class="bg-white text-slate-900 p-6 rounded-xl inline-block shadow-inner mb-6 w-full max-w-sm">
            <div class="flex justify-between items-center mb-4">
                <span class="font-black text-xl tracking-wider text-red-600">QRIS</span>
                <span class="text-xs font-semibold bg-slate-100 text-slate-700 px-2.5 py-1 rounded">AMR Production</span>
            </div>
            
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=AMR_PRODUCTION_QRIS" alt="QRIS" class="mx-auto w-40 h-40 object-contain my-2 border p-1 rounded">
            
            <div class="mt-3 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-500 uppercase font-bold">Total Pembayaran:</p>
                <p class="text-2xl font-black text-emerald-600 mt-0.5">Rp <?= number_format($order['total_harga'] ?? $order['total_price'] ?? 0, 0, ',', '.'); ?></p>
            </div>
          </div>

          <?php if (($order['status'] ?? 'Pending') === 'Pending'): ?>
            <form method="POST">
              <button type="submit" name="simulasi_lunas" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-200 text-base mb-3 cursor-pointer">
                ✅ Saya Sudah Bayar (Simulasi Lunas)
              </button>
            </form>
          <?php else: ?>
            <div class="bg-emerald-900/50 border border-emerald-500 text-emerald-300 py-3 rounded-lg font-bold mb-3 text-sm">
                Status: Lunas / Paid
            </div>
          <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="catalog.php" class="text-xs text-slate-400 hover:text-white underline">← Kembali ke Katalog</a>
        </div>
      <?php endif; ?>

    </div>
  </div>

</body>
</html>