<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'config.php';
$db_conn = isset($conn) ? $conn : (isset($mysqli) ? $mysqli : null);

$msg = '';

// Proses Tambah / Edit Produk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $prod_id = intval($_POST['product_id'] ?? 0);
    $nama_alat = mysqli_real_escape_string($db_conn, $_POST['name'] ?? '');
    $kategori = mysqli_real_escape_string($db_conn, $_POST['category'] ?? '');
    $harga = floatval($_POST['price'] ?? 0);
    $gambar = mysqli_real_escape_string($db_conn, $_POST['image_url'] ?? '');

    if ($prod_id > 0) {
        $query = "UPDATE products SET name='$nama_alat', category='$kategori', price='$harga', image_url='$gambar' WHERE id=$prod_id";
        if (mysqli_query($db_conn, $query)) {
            $msg = "Produk #$prod_id berhasil diperbarui!";
        }
    } else {
        $query = "INSERT INTO products (name, category, price, image_url) VALUES ('$nama_alat', '$kategori', '$harga', '$gambar')";
        if (mysqli_query($db_conn, $query)) {
            $msg = "Alat sewa baru berhasil ditambahkan ke katalog!";
        }
    }
}

// Proses Hapus Produk
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($db_conn, "DELETE FROM products WHERE id = $del_id");
    header("Location: admin_catalog.php?msg=deleted");
    exit;
}

$products = mysqli_query($db_conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Katalog - Admin AMR Production</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f1f3f5; color: #333; }
        .sidebar { width: 240px; background: #2c3e50; color: white; position: fixed; height: 100%; padding: 20px 0; top: 0; left: 0; }
        .sidebar h3 { text-align: center; color: #ecf0f1; margin-bottom: 30px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px 20px; text-decoration: none; font-size: 15px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left: 4px solid #e74c3c; }
        .main-content { margin-left: 260px; padding: 30px; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 15px 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .top-bar h2 { margin: 0; font-size: 20px; color: #2c3e50; }
        
        .card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #cbd5e1; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: #475569; }
        .form-control { width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        
        .spreadsheet-container { background: white; border: 1px solid #cbd5e1; border-radius: 6px; overflow-x: auto; }
        table.excel-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        table.excel-table th, table.excel-table td { border: 1px solid #cbd5e1; padding: 10px 12px; vertical-align: middle; }
        table.excel-table th { background-color: #e2e8f0; color: #1e293b; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        table.excel-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .btn { padding: 7px 12px; border-radius: 4px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; font-size: 12px; }
        .btn-success { background: #16a34a; color: white; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-danger { background: #dc2626; color: white; }
        .alert { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>AMR ADMIN</h3>
        <a href="admin_orders.php">📦 Kelola Pesanan</a>
        <a href="admin_catalog.php" class="active">🛠️ Kelola Katalog Alat</a>
        <a href="login.php?logout=1" style="color: #e74c3c; margin-top: 40px;">🚪 Keluar</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Manajemen Katalog Alat Sewa</h2>
        </div>

        <?php if(!empty($msg) || isset($_GET['msg'])): ?>
            <div class="alert"><?php echo $msg ?: "Aksi berhasil dilakukan!"; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3 id="form-title" style="margin-top:0; color:#1e293b; border-bottom:1px solid #eee; padding-bottom:10px;">➕ Tambah Alat Sewa Baru</h3>
            <form method="POST">
                <input type="hidden" name="product_id" id="prod_id" value="">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Alat:</label>
                        <input type="text" name="name" id="prod_name" class="form-control" required placeholder="Contoh: Sound System Line Array">
                    </div>
                    <div class="form-group">
                        <label>Kategori:</label>
                        <input type="text" name="category" id="prod_cat" class="form-control" required placeholder="Contoh: Sound, Lighting, Stage">
                    </div>
                    <div class="form-group">
                        <label>Harga Sewa per Hari (Rp):</label>
                        <input type="number" name="price" id="prod_price" class="form-control" required placeholder="Contoh: 1500000">
                    </div>
                    <div class="form-group">
                        <label>URL Gambar (Link Foto):</label>
                        <input type="text" name="image_url" id="prod_img" class="form-control" required placeholder="https://images.unsplash.com/...">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 5px;">
                    <button type="submit" name="save_product" class="btn btn-success" id="btn-submit" style="padding: 10px 20px; font-size: 14px;">Simpan Alat Baru</button>
                    <button type="button" onclick="resetForm()" class="btn" style="background: #64748b; color: white; display: none;" id="btn-cancel">Batal Edit</button>
                </div>
            </form>
        </div>

        <div class="spreadsheet-container">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">ID</th>
                        <th style="width: 70px; text-align: center;">Foto</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Harga / Hari</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && mysqli_num_rows($products) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($products)): 
                            // Deteksi fleksibel nama kolom database
                            $p_name = $p['name'] ?? $p['nama_alat'] ?? $p['nama'] ?? '';
                            $p_cat = $p['category'] ?? $p['kategori'] ?? '';
                            $p_price = floatval($p['price'] ?? $p['harga'] ?? 0);
                            $p_img = $p['image_url'] ?? $p['gambar'] ?? '';
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: bold; background: #f8fafc;">#<?php echo $p['id']; ?></td>
                                <td style="text-align: center;">
                                    <img src="<?php echo htmlspecialchars($p_img); ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid #cbd5e1;" onerror="this.src='https://via.placeholder.com/45'">
                                </td>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($p_name ?: 'Tanpa Nama'); ?></td>
                                <td><?php echo htmlspecialchars($p_cat ?: '-'); ?></td>
                                <td style="font-weight: bold; color: #047857;">Rp <?php echo number_format($p_price, 0, ',', '.'); ?></td>
                                <td style="text-align: center;">
                                    <button type="button" onclick="editProduct(<?php echo $p['id']; ?>, '<?php echo addslashes($p_name); ?>', '<?php echo addslashes($p_cat); ?>', <?php echo $p_price; ?>, '<?php echo addslashes($p_img); ?>')" class="btn btn-primary" style="padding: 5px 10px;">Edit</button>
                                    <a href="admin_catalog.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Yakin ingin menghapus alat ini dari katalog?')" class="btn btn-danger" style="padding: 5px 10px; display: inline-block;">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 25px; color: #64748b;">Belum ada alat di dalam katalog.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editProduct(id, name, cat, price, img) {
            document.getElementById('prod_id').value = id;
            document.getElementById('prod_name').value = name;
            document.getElementById('prod_cat').value = cat;
            document.getElementById('prod_price').value = price;
            document.getElementById('prod_img').value = img;
            
            document.getElementById('form-title').innerText = '✏️ Edit Alat Sewa (ID #' + id + ')';
            document.getElementById('btn-submit').innerText = 'Perbarui Alat';
            document.getElementById('btn-cancel').style.display = 'inline-block';
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function resetForm() {
            document.getElementById('prod_id').value = '';
            document.getElementById('prod_name').value = '';
            document.getElementById('prod_cat').value = '';
            document.getElementById('prod_price').value = '';
            document.getElementById('prod_img').value = '';
            
            document.getElementById('form-title').innerText = '➕ Tambah Alat Sewa Baru';
            document.getElementById('btn-submit').innerText = 'Simpan Alat Baru';
            document.getElementById('btn-cancel').style.display = 'none';
        }
    </script>

</body>
</html>