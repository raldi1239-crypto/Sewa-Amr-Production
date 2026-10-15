<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'config.php';
$db_conn = isset($conn) ? $conn : (isset($mysqli) ? $mysqli : null);

$msg = '';

// --- PROSES UPDATE STATUS PESANAN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id'] ?? 0);
    $new_status = mysqli_real_escape_string($db_conn, $_POST['status'] ?? 'Pending');
    
    if ($order_id > 0) {
        $update_query = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
        if (mysqli_query($db_conn, $update_query)) {
            $msg = "Status pesanan #$order_id berhasil diperbarui menjadi $new_status!";
        }
    }
}

// --- EXPORT EXCEL (.CSV) ---
if (isset($_GET['action']) && $_GET['action'] == 'export_excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Rekap_Pesanan_AMR_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Order', 'Nama Pemesan', 'No Telepon', 'Tgl Sewa', 'Total Biaya', 'Status']);

    $res = mysqli_query($db_conn, "SELECT * FROM orders ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, [
            $row['id'],
            $row['nama_pemesan'] ?? $row['customer_name'] ?? 'Pelanggan',
            $row['whatsapp'] ?? $row['phone'] ?? '-',
            $row['tanggal_sewa'] ?? $row['rental_date'] ?? '-',
            'Rp ' . number_format($row['total_harga'] ?? $row['total_price'] ?? 0, 0, ',', '.'),
            $row['status'] ?? 'Pending'
        ]);
    }
    fclose($output);
    exit;
}

// 1. Ambil Pesanan Aktif (Belum Selesai / Batal)
$active_orders = mysqli_query($db_conn, "SELECT * FROM orders WHERE status NOT IN ('Selesai', 'Batal') OR status IS NULL ORDER BY id DESC");

// 2. Ambil Riwayat Pesanan (Selesai & Batal)
$completed_orders = mysqli_query($db_conn, "SELECT * FROM orders WHERE status IN ('Selesai', 'Batal') ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Admin AMR Production</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f1f3f5; color: #333; }
        .sidebar { width: 240px; background: #2c3e50; color: white; position: fixed; height: 100%; padding: 20px 0; top: 0; left: 0; }
        .sidebar h3 { text-align: center; color: #ecf0f1; margin-bottom: 30px; }
        .sidebar a { display: block; color: #bdc3c7; padding: 12px 20px; text-decoration: none; font-size: 15px; }
        .sidebar a:hover, .sidebar a.active { background: #34495e; color: white; border-left: 4px solid #e74c3c; }
        .main-content { margin-left: 260px; padding: 30px; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: white; padding: 15px 25px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .top-bar h2 { margin: 0; font-size: 20px; color: #2c3e50; }
        
        .section-title { font-size: 16px; font-weight: bold; color: #1e293b; margin: 25px 0 10px 0; display: flex; align-items: center; gap: 8px; }
        
        .spreadsheet-container { background: white; border: 1px solid #cbd5e1; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow-x: auto; margin-bottom: 30px; }
        table.excel-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
        table.excel-table th, table.excel-table td { border: 1px solid #cbd5e1; padding: 10px 12px; }
        table.excel-table th { background-color: #e2e8f0; color: #1e293b; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        table.excel-table tr:nth-child(even) { background-color: #f8fafc; }
        
        .btn { padding: 7px 12px; border-radius: 4px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; font-size: 12px; }
        .btn-success { background: #16a34a; color: white; }
        .btn-primary { background: #2563eb; color: white; }
        .alert { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        select.excel-select { padding: 5px; border-radius: 4px; border: 1px solid #94a3b8; background: white; font-size: 13px; }
        
        .badge-done { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; }
        .badge-cancel { background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3>AMR ADMIN</h3>
        <a href="admin_orders.php" class="active">📦 Kelola Pesanan</a>
        <a href="admin_catalog.php">🛠️ Kelola Katalog Alat</a>
        <a href="login.php?logout=1" style="color: #e74c3c; margin-top: 40px;">🚪 Keluar</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Rekapitulasi Data Pesanan</h2>
            <a href="admin_orders.php?action=export_excel" class="btn btn-success">📥 Download ke Excel (.csv)</a>
        </div>

        <?php if(!empty($msg)): ?>
            <div class="alert"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- TABEL 1: PESANAN AKTIF -->
        <div class="section-title">⏳ Pesanan Aktif / Membutuhkan Aksi</div>
        <div class="spreadsheet-container">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">ID</th>
                        <th>Nama Pemesan</th>
                        <th>No Telepon</th>
                        <th>Tgl Sewa</th>
                        <th>Total Biaya</th>
                        <th style="width: 220px;">Status Pesanan</th>
                        <th style="width: 90px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($active_orders && mysqli_num_rows($active_orders) > 0): ?>
                        <?php while ($o = mysqli_fetch_assoc($active_orders)): ?>
                            <tr>
                                <td style="text-align: center; font-weight: bold; background: #f8fafc;">#<?php echo $o['id']; ?></td>
                                <td><?php echo htmlspecialchars($o['nama_pemesan'] ?? $o['customer_name'] ?? 'Pelanggan'); ?></td>
                                <td><?php echo htmlspecialchars($o['whatsapp'] ?? $o['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($o['tanggal_sewa'] ?? $o['rental_date'] ?? '-'); ?></td>
                                <td style="font-weight: bold; color: #047857;">Rp <?php echo number_format($o['total_harga'] ?? $o['total_price'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" class="excel-select">
                                            <option value="Pending" <?php if(($o['status']??'') == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Menunggu Verifikasi" <?php if(($o['status']??'') == 'Menunggu Verifikasi') echo 'selected'; ?>>Menunggu Verifikasi</option>
                                            <option value="Paid" <?php if(($o['status']??'') == 'Paid') echo 'selected'; ?>>Paid (Lunas)</option>
                                            <option value="Disetujui" <?php if(($o['status']??'') == 'Disetujui') echo 'selected'; ?>>Disetujui</option>
                                            <option value="Selesai" <?php if(($o['status']??'') == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                            <option value="Batal" <?php if(($o['status']??'') == 'Batal') echo 'selected'; ?>>Batal</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary">Simpan</button>
                                    </form>
                                </td>
                                <td style="text-align: center;">
                                    <span style="color: #2563eb; font-weight: bold; background: #eff6ff; padding: 2px 6px; border-radius: 4px; font-size: 11px;">Diproses</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding: 20px; color: #64748b;">Tidak ada pesanan aktif saat ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- TABEL 2: RIWAYAT SELESAI & BATAL -->
        <div class="section-title">✅ Riwayat Pesanan Selesai & Batal</div>
        <div class="spreadsheet-container">
            <table class="excel-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">ID</th>
                        <th>Nama Pemesan</th>
                        <th>No Telepon</th>
                        <th>Tgl Sewa</th>
                        <th>Total Biaya</th>
                        <th style="width: 220px;">Status Akhir</th>
                        <th style="width: 90px; text-align: center;">Ubah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($completed_orders && mysqli_num_rows($completed_orders) > 0): ?>
                        <?php while ($o = mysqli_fetch_assoc($completed_orders)): ?>
                            <tr style="background-color: #fafafa;">
                                <td style="text-align: center; font-weight: bold; background: #f1f5f9;">#<?php echo $o['id']; ?></td>
                                <td><?php echo htmlspecialchars($o['nama_pemesan'] ?? $o['customer_name'] ?? 'Pelanggan'); ?></td>
                                <td><?php echo htmlspecialchars($o['whatsapp'] ?? $o['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($o['tanggal_sewa'] ?? $o['rental_date'] ?? '-'); ?></td>
                                <td style="font-weight: bold; color: #475569;">Rp <?php echo number_format($o['total_harga'] ?? $o['total_price'] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <?php if ($o['status'] === 'Selesai'): ?>
                                        <span class="badge-done">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge-cancel">Batal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" class="excel-select" style="font-size:11px;">
                                            <option value="Pending">Kembalikan ke Pending</option>
                                            <option value="Paid">Lunas</option>
                                            <option value="Disetujui">Disetujui</option>
                                            <option value="Selesai" <?php if($o['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                            <option value="Batal" <?php if($o['status'] == 'Batal') echo 'selected'; ?>>Batal</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary" style="padding:4px 8px; font-size:11px;">Set</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding: 20px; color: #64748b;">Belum ada riwayat pesanan selesai/batal.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>