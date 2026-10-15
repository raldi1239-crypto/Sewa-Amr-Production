<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AMR Production - Sewa Sound System & Alat Event Professional</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 flex flex-col min-h-screen relative">

  <!-- Navbar -->
  <div class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur border-b border-slate-800">
    <?php include 'navbar.php'; ?>
  </div>

  <!-- Hero Section -->
  <section class="relative bg-gradient-to-b from-slate-900 via-indigo-950 to-slate-900 py-24 md:py-32 px-4 text-center overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-4xl mx-auto">
      <span class="inline-block bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-6">
        <i class="fa-solid fa-star text-amber-400 mr-1"></i> Partner Event No. 1 Terpercaya
      </span>
      <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white mb-6 leading-tight">
        Wujudkan Event Spektakuler Bersama <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-pink-500">AMR Production</span>
      </h1>
      <p class="text-slate-300 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
        Penyedia layanan sewa Sound System, Lighting, Panggung Modular, dan perlengkapan panggung profesional untuk konser, wedding, hingga seminar corporate.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="catalog.php" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all transform hover:-translate-y-1">
          <i class="fa-solid fa-cubes mr-2"></i> Jelajahi Katalog
        </a>
        <a href="#fitur" class="w-full sm:w-auto border border-slate-700 bg-slate-800/50 hover:bg-slate-800 text-slate-200 font-semibold px-8 py-4 rounded-xl transition">
          Pelajari Lebih Lanjut
        </a>
      </div>
    </div>
  </section>

  <!-- Section Keunggulan / Fitur -->
  <section id="fitur" class="py-20 px-4 bg-slate-950">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-16">
        <h2 class="text-3xl font-extrabold text-white">Mengapa Memilih AMR Production?</h2>
        <p class="text-slate-400 mt-2">Kualitas peralatan dan layanan maksimal untuk menjamin kelancaran acaramu.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-slate-900 border border-slate-800 p-8 rounded-2xl hover:border-indigo-500/50 transition duration-300">
          <div class="w-14 h-14 bg-indigo-600/20 rounded-xl flex items-center justify-center text-indigo-400 text-2xl mb-6">
            <i class="fa-solid fa-sliders"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Peralatan High-End</h3>
          <p class="text-slate-400 text-sm leading-relaxed">Perangkat audio dan panggung kelas profesional dari brand ternama dunia yang terawat dan siap pakai.</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-8 rounded-2xl hover:border-indigo-500/50 transition duration-300">
          <div class="w-14 h-14 bg-pink-600/20 rounded-xl flex items-center justify-center text-pink-400 text-2xl mb-6">
            <i class="fa-solid fa-headset"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Teknisi Berpengalaman</h3>
          <p class="text-slate-400 text-sm leading-relaxed">Tim sound engineer dan teknisi lighting siap melakukan instalasi dan mengawal acara hingga selesai.</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-8 rounded-2xl hover:border-indigo-500/50 transition duration-300">
          <div class="w-14 h-14 bg-emerald-600/20 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-6">
            <i class="fa-solid fa-bolt"></i>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Harga Transparan</h3>
          <p class="text-slate-400 text-sm leading-relaxed">Sistem harga sewa harian yang terjangkau tanpa ada biaya tersembunyi. Proses booking instan & cepat.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Produk Populer -->
  <section class="py-20 px-4 bg-slate-900">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12">
        <div>
          <h2 class="text-3xl font-extrabold text-white">Peralatan Favorit</h2>
          <p class="text-slate-400 mt-2">Paling sering disewa oleh event organizer & penyelenggara acara.</p>
        </div>
        <a href="catalog.php" class="mt-4 md:mt-0 text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-2">
          Lihat Semua Produk <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $popular = mysqli_query($conn, "SELECT * FROM products LIMIT 4");
        if ($popular && mysqli_num_rows($popular) > 0):
            while ($p = mysqli_fetch_assoc($popular)):
        ?>
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden flex flex-col justify-between hover:border-indigo-500/40 transition">
          <div class="p-6">
            <span class="text-[10px] font-bold bg-indigo-500/20 text-indigo-300 px-3 py-1 rounded-full uppercase tracking-wider"><?= htmlspecialchars($p['kategori']); ?></span>
            <h3 class="text-lg font-bold text-white mt-4"><?= htmlspecialchars($p['nama_produk']); ?></h3>
            <p class="text-slate-400 text-xs mt-2 line-clamp-2"><?= htmlspecialchars($p['deskripsi']); ?></p>
          </div>
          <div class="p-6 border-t border-slate-800/80 bg-slate-900/50 flex justify-between items-center">
            <div>
              <p class="text-xs text-slate-500">Mulai dari</p>
              <p class="text-indigo-400 font-black text-base">Rp <?= number_format($p['harga_per_hari'], 0, ',', '.'); ?><span class="text-[10px] text-slate-500 font-normal">/hari</span></p>
            </div>
            <a href="catalog.php" class="bg-indigo-600 hover:bg-indigo-500 text-white w-9 h-9 rounded-lg flex items-center justify-center transition">
              <i class="fa-solid fa-plus text-sm"></i>
            </a>
          </div>
        </div>
        <?php endwhile; else: ?>
            <p class="text-slate-400 col-span-4 text-center py-6">Belum ada produk tersedia di database.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- CTA Call to Action -->
  <section class="py-20 px-4 bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 border-y border-indigo-500/20 text-center">
    <div class="max-w-3xl mx-auto">
      <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Butuh Konsultasi Paket Event?</h2>
      <p class="text-indigo-200 text-base mb-8">Hubungi tim kami untuk konsultasi spesifikasi teknis peralatan yang sesuai dengan budget dan kapasitas venue kamu.</p>
      <a href="catalog.php" class="bg-white text-indigo-950 font-bold px-8 py-3.5 rounded-xl hover:bg-slate-100 transition shadow-lg">
        Sewa Sekarang Juga
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-slate-950 text-slate-400 py-10 px-4 border-t border-slate-800">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
      <div>
        <h3 class="text-lg font-bold text-indigo-400 mb-3">AMR Production</h3>
        <p class="text-sm text-slate-400 leading-relaxed">Solusi penyewaan alat event terlengkap, terpercaya, dan profesional untuk menciptakan pengalaman acara tak terlupakan.</p>
      </div>
      <div>
        <h4 class="text-white font-semibold mb-3">Navigasi Cepat</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="catalog.php" class="hover:text-indigo-400 transition">Katalog Produk</a></li>
          <li><a href="cart.php" class="hover:text-indigo-400 transition">Keranjang</a></li>
          <li><a href="login.php" class="hover:text-indigo-400 transition">Login / Register</a></li>
        </ul>
      </div>
      <div>
        <h4 class="text-white font-semibold mb-3">Kontak Kami</h4>
        <p class="text-sm text-slate-400"><i class="fa-solid fa-location-dot mr-2 text-indigo-400"></i> Jl. Event Utama No. 1, Jakarta</p>
        <p class="text-sm text-slate-400 mt-2"><i class="fa-solid fa-envelope mr-2 text-indigo-400"></i> info@amrproduction.com</p>
      </div>
    </div>
    <div class="border-t border-slate-800/80 pt-6 text-center text-xs text-slate-500">
      &copy; <?= date('Y'); ?> AMR Production. All rights reserved.
    </div>
  </footer>
<!-- ========================================== -->
  <!-- WIDGET CHATBOT AI (TAILWIND STYLE PREMIUM)  -->
  <!-- ========================================== -->
  <div class="fixed bottom-6 right-6 z-50">
    <!-- Tombol Ikon Chat -->
    <button id="btnAmrChat" type="button" class="bg-gradient-to-r from-indigo-600 to-pink-600 hover:from-indigo-500 hover:to-pink-500 text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center text-xl transition transform hover:scale-110 focus:outline-none">
      <i class="fa-solid fa-robot"></i>
    </button>

    <!-- Kotak Jendela Chat -->
    <div id="boxAmrChat" class="hidden absolute bottom-20 right-0 w-80 sm:w-96 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl flex flex-col overflow-hidden">
      <!-- Header Chat -->
      <div class="bg-slate-950 px-4 py-3 border-b border-slate-800 flex justify-between items-center">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
          <span class="text-sm font-bold text-white">AMR AI Assistant</span>
        </div>
        <button id="btnCloseChat" type="button" class="text-slate-400 hover:text-white text-sm font-bold px-2 py-1">✕</button>
      </div>

      <!-- Ruang Pesan Masuk -->
      <div id="bodyAmrChat" class="p-4 h-80 overflow-y-auto flex flex-col gap-3 text-sm bg-slate-900/90">
        <div class="bg-slate-800 text-slate-200 p-3 rounded-xl max-w-[85%] self-start border border-slate-700 leading-relaxed">
          Halo! Saya Asisten AI AMR Production. Ada informasi alat event atau konsultasi panggung yang bisa saya bantu jelaskan untuk Anda? 😊
        </div>
      </div>

      <!-- Area Input & Kirim -->
      <div class="p-3 bg-slate-950 border-t border-slate-800 flex gap-2">
        <input type="text" id="inputAmrChat" placeholder="Tanyakan sesuatu..." class="flex-1 bg-slate-900 border border-slate-700 text-slate-100 text-xs px-3 py-2.5 rounded-xl focus:outline-none focus:border-indigo-500">
        <button id="sendAmrChat" type="button" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>
  </div>

  <script>
  (function() {
      const btnChat = document.getElementById('btnAmrChat');
      const boxChat = document.getElementById('boxAmrChat');
      const closeChat = document.getElementById('btnCloseChat');
      const inputChat = document.getElementById('inputAmrChat');
      const sendChat = document.getElementById('sendAmrChat');
      const bodyChat = document.getElementById('bodyAmrChat');

      if (!btnChat || !boxChat) return;

      btnChat.addEventListener('click', function(e) {
          e.stopPropagation();
          boxChat.classList.toggle('hidden');
          if (!boxChat.classList.contains('hidden')) {
              inputChat.focus();
          }
      });

      if (closeChat) {
          closeChat.addEventListener('click', function() {
              boxChat.classList.add('hidden');
          });
      }

      async function processMessage() {
          const text = inputChat.value.trim();
          if (!text) return;

          // Tampilkan pesan user
          bodyChat.innerHTML += `<div class="bg-indigo-600 text-white p-3 rounded-xl max-w-[85%] self-end leading-relaxed">${escapeHtml(text)}</div>`;
          inputChat.value = '';
          bodyChat.scrollTop = bodyChat.scrollHeight;

          // Indikator AI mengetik
          const loadId = 'load-' + Date.now();
          bodyChat.innerHTML += `<div id="${loadId}" class="bg-slate-800 text-slate-400 p-3 rounded-xl max-w-[85%] self-start border border-slate-700 text-xs italic">AI sedang menganalisis...</div>`;
          bodyChat.scrollTop = bodyChat.scrollHeight;

          try {
              const response = await fetch('ai_assistant_backend.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ message: text })
              });
              
              const data = await response.json();
              const loader = document.getElementById(loadId);
              if (loader) loader.remove();

              bodyChat.innerHTML += `<div class="bg-slate-800 text-slate-200 p-3 rounded-xl max-w-[85%] self-start border border-slate-700 whitespace-pre-line leading-relaxed">${escapeHtml(data.reply)}</div>`;
          } catch(err) {
              const loader = document.getElementById(loadId);
              if (loader) loader.remove();
              bodyChat.innerHTML += `<div class="bg-slate-800 text-red-400 p-3 rounded-xl max-w-[85%] self-start border border-slate-700">Maaf, terjadi kendala saat memproses jawaban. Silakan coba lagi.</div>`;
          }
          bodyChat.scrollTop = bodyChat.scrollHeight;
      }

      if (sendChat) {
          sendChat.addEventListener('click', processMessage);
      }

      if (inputChat) {
          inputChat.addEventListener('keydown', function(e) {
              if (e.key === 'Enter') {
                  e.preventDefault();
                  processMessage();
              }
          });
      }

      function escapeHtml(str) {
          return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
      }
  })();
  </script>