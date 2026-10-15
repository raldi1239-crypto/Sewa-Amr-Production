<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Halo! Selamat datang di AMR Production. Ada yang bisa saya bantu terkait kebutuhan event Anda hari ini?']);
    exit;
}

// 1. Ambil seluruh data produk dari database
$db_conn = isset($conn) ? $conn : (isset($mysqli) ? $mysqli : null);
$productsList = [];

if ($db_conn) {
    $res = mysqli_query($db_conn, "SELECT * FROM products");
    if ($res && mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            $productsList[] = [
                'id' => $row['id'] ?? '',
                'nama' => $row['nama_produk'] ?? 'Alat Event',
                'harga' => $row['harga_per_hari'] ?? 0,
                'deskripsi' => $row['deskripsi'] ?? ''
            ];
        }
    }
}

$userMsgLower = strtolower($userMessage);

// 2. Respon Salam Pembuka
if (preg_match('/^(hi|halo|pagi|siang|malam|sore|assalamu)/i', $userMsgLower)) {
    echo json_encode([
        'reply' => "Halo! Selamat datang di AMR Production.\n\nKami siap membantu kebutuhan sewa peralatan event Anda seperti Sound System, Lighting, Panggung, Tenda, dan lainnya.\n\nSilakan ketik nama alat yang Anda cari!"
    ]);
    exit;
}

// 3. Logika Pencarian Cerdas & Fleksibel
$matchedProducts = [];
$seenNames = [];

// Pemetaan sinonim kata kunci kategori
$categoryMap = [
    'tenda' => ['tenda', 'roder', 'sarnafil', 'canopy', 'kanopi', 'terpal'],
    'sound' => ['sound', 'speaker', 'mixer', 'audio', 'mic', 'microphone'],
    'panggung' => ['panggung', 'rigging', 'stage', 'flooring', 'barikade'],
    'lighting' => ['lighting', 'light', 'lampu', 'moving head', 'par led', 'fresh']
];

// Cek apakah input user mengandung kata kunci kategori
$activeCategory = null;
foreach ($categoryMap as $catKey => $synonyms) {
    foreach ($synonyms as $syn) {
        if (strpos($userMsgLower, $syn) !== false) {
            $activeCategory = $catKey;
            break 2;
        }
    }
}

foreach ($productsList as $prod) {
    $prodNameLower = strtolower($prod['nama']);
    $prodDescLower = strtolower($prod['deskripsi']);
    
    // Hindari duplikasi produk ganda
    if (in_array($prodNameLower, $seenNames)) {
        continue;
    }

    $isMatch = false;

    if ($activeCategory !== null) {
        // Jika terdeteksi kategori (misal: tenda), cari kata sinonimnya di nama/deskripsi produk
        foreach ($categoryMap[$activeCategory] as $syn) {
            if (strpos($prodNameLower, $syn) !== false || strpos($prodDescLower, $syn) !== false) {
                $isMatch = true;
                break;
            }
        }
    } else {
        // Jika pencarian kata bebas/spesifik
        $keywords = explode(' ', $userMsgLower);
        foreach ($keywords as $kw) {
            if (strlen($kw) > 2 && (strpos($prodNameLower, $kw) !== false || strpos($prodDescLower, $kw) !== false)) {
                $isMatch = true;
                break;
            }
        }
    }

    if ($isMatch) {
        $matchedProducts[] = $prod;
        $seenNames[] = $prodNameLower;
    }
}

// 4. Format Output Teks Biasa Tanpa Simbol/Markdown
if (!empty($matchedProducts)) {
    $aiReply = "Rekomendasi unit yang tersedia untuk Anda:\n\n";
    
    foreach (array_slice($matchedProducts, 0, 4) as $p) {
        $hargaFormatted = number_format($p['harga'], 0, ',', '.');
        
        $aiReply .= "Nama Alat: " . $p['nama'] . "\n";
        $aiReply .= "Harga: Rp " . $hargaFormatted . " / hari\n";
        if (!empty($p['deskripsi'])) {
            $aiReply .= "Deskripsi: " . $p['deskripsi'] . "\n";
        }
        $aiReply .= "\n";
    }
    
    $aiReply .= "Untuk melakukan pemesanan dan mengecek ketersediaan tanggal, silakan buka menu Katalog Alat.";
} else {
    $aiReply = "Mohon maaf, unit yang Anda cari belum ditemukan di daftar cepat kami.\n\nAnda dapat mengecek seluruh inventaris alat seperti Sound System, Lighting, Panggung, dan Tenda secara lengkap pada menu Katalog Alat.";
}

echo json_encode(['reply' => trim($aiReply)]);
exit;
?>