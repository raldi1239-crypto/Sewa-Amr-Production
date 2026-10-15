CREATE DATABASE IF NOT EXISTS `sewa_event_amr`;
USE `sewa_event_amr`;

ALTER TABLE orders ADD COLUMN IF NOT EXISTS tgl_sewa DATE;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS tgl_kembali DATE;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS total_harga DECIMAL(10,2);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_produk` VARCHAR(150) NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `harga_per_hari` DECIMAL(10,2) NOT NULL,
  `stok` INT NOT NULL,
  `deskripsi` TEXT,
  `gambar` VARCHAR(255) DEFAULT 'default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `tgl_sewa` DATE NOT NULL,
  `tgl_kembali` DATE NOT NULL,
  `total_harga` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pending', 'Paid', 'Completed', 'Cancelled') DEFAULT 'Pending',
  `bukti_pembayaran` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `harga` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- Akun Default (Password: admin123 & user123)
INSERT INTO `users` (`nama`, `email`, `password`, `role`) VALUES
('Admin AMR', 'admin@amrproduction.com', '$2y$10$e846/7yMUp070M72Wj7xuu0.xO7b6U2jG42071850123456789012', 'admin'),
('User Coba', 'user@gmail.com', '$2y$10$e846/7yMUp070M72Wj7xuu0.xO7b6U2jG42071850123456789012', 'user');

-- Sample Produk Peralatan Event
INSERT INTO `products` (`nama_produk`, `kategori`, `harga_per_hari`, `stok`, `deskripsi`) VALUES
('Sound System 1000W', 'Sound System', 350000.00, 5, 'Paket Sound System cocok untuk acara seminar dan outdoor.'),
('Mic Wireless Line 6', 'Audio', 75000.00, 10, 'Microphone wireless jernih jangkauan hingga 50 meter.'),
('Panggung Modular 3x4m', 'Panggung', 500000.00, 2, 'Panggung kokoh tinggi 50cm dengan karpet hitam.'),
('Lighting Par LED 54x3W', 'Lighting', 100000.00, 8, 'Lampu warna-warni untuk pencahayaan panggung.');