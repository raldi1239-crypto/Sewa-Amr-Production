<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- FontAwesome Icons & Google Font -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
* {
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.navbar-amr {
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 15px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.brand-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}
.brand-icon {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: white;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
}
.brand-text {
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.5px;
}
.brand-text span {
    background: linear-gradient(135deg, #a855f7, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.nav-links {
    display: flex;
    align-items: center;
    gap: 30px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.nav-links a {
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
}
.nav-links a:hover {
    color: #a855f7;
}
.nav-btns {
    display: flex;
    gap: 12px;
}
.btn-login {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: white !important;
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    transition: transform 0.2s;
}
.btn-login:hover {
    transform: translateY(-2px);
}
.btn-register {
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white !important;
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 600;
}
</style>

<nav class="navbar-amr">
    <a href="index.php" class="brand-logo">
        <div class="brand-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
        <div class="brand-text">AMR <span>Production</span></div>
    </a>
    <ul class="nav-links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i> Beranda</a></li>
        <li><a href="catalog.php"><i class="fa-solid fa-boxes-stacked"></i> Katalog Alat</a></li>
        <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a></li>
    </ul>
    <div class="nav-btns">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="my_orders.php" class="btn-register"><i class="fa-solid fa-receipt"></i> Pesanan Saya</a>
            <a href="logout.php" class="btn-login" style="background:#ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Masuk</a>
            <a href="register.php" class="btn-register">Daftar</a>
        <?php endif; ?>
    </div>
</nav>