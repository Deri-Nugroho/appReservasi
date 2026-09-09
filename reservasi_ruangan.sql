-- =====================================================
-- SQL Schema: Sistem Reservasi Ruangan (Hotel/Meeting Room)
-- Database: MariaDB / MySQL
-- Jalankan file ini di phpMyAdmin atau MySQL client
-- SEBELUM membuat aplikasi PHP-nya
-- =====================================================

-- 1. Buat Database
CREATE DATABASE IF NOT EXISTS db_reservasi_ruangan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE db_reservasi_ruangan;

-- =====================================================
-- 2. Tabel Users (untuk login admin/staff sederhana)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 3. Tabel Ruangan (rooms)
-- =====================================================
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(100) NOT NULL,
    tipe ENUM('Meeting Room', 'Kamar Hotel', 'Aula', 'Lainnya') DEFAULT 'Meeting Room',
    kapasitas INT NOT NULL DEFAULT 1,
    harga_per_jam DECIMAL(12,2) DEFAULT 0,
    fasilitas TEXT NULL,
    status ENUM('tersedia', 'perbaikan', 'nonaktif') DEFAULT 'tersedia',
    gambar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 4. Tabel Booking / Reservasi (bookings)
-- =====================================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    email_pemesan VARCHAR(100) NULL,
    no_telepon VARCHAR(20) NULL,
    keperluan VARCHAR(255) NULL,
    tanggal_mulai DATETIME NOT NULL,
    tanggal_selesai DATETIME NOT NULL,
    status ENUM('pending', 'dikonfirmasi', 'dibatalkan', 'selesai') DEFAULT 'pending',
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_room FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 5. Index tambahan untuk performa query jadwal
-- =====================================================
CREATE INDEX idx_booking_room_tanggal ON bookings (room_id, tanggal_mulai, tanggal_selesai);
CREATE INDEX idx_booking_status ON bookings (status);

-- =====================================================
-- 6. Data Awal (Sample Data)
-- =====================================================

-- User default (password disimpan plain text, tanpa hash)
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', 'admin123', 'admin');

-- Data ruangan contoh
INSERT INTO rooms (nama_ruangan, tipe, kapasitas, harga_per_jam, fasilitas, status) VALUES
('Meeting Room A', 'Meeting Room', 10, 150000, 'Proyektor, Whiteboard, AC, Wifi', 'tersedia'),
('Meeting Room B', 'Meeting Room', 20, 250000, 'Proyektor, Sound System, AC, Wifi', 'tersedia'),
('Aula Utama', 'Aula', 100, 1000000, 'Sound System, Panggung, AC, Wifi', 'tersedia'),
('Kamar Deluxe 101', 'Kamar Hotel', 2, 500000, 'AC, TV, Kamar Mandi Dalam, Wifi', 'tersedia'),
('Kamar Suite 201', 'Kamar Hotel', 4, 900000, 'AC, TV, Kamar Mandi Dalam, Wifi, Balkon', 'tersedia');

-- Data booking contoh
INSERT INTO bookings (room_id, nama_pemesan, email_pemesan, no_telepon, keperluan, tanggal_mulai, tanggal_selesai, status) VALUES
(1, 'Budi Santoso', 'budi@email.com', '081234567890', 'Rapat Tim Marketing', '2026-09-15 09:00:00', '2026-09-15 11:00:00', 'dikonfirmasi'),
(2, 'Siti Aminah', 'siti@email.com', '081298765432', 'Presentasi Klien', '2026-09-16 13:00:00', '2026-09-16 15:00:00', 'pending'),
(4, 'John Doe', 'john@email.com', '081211112222', 'Menginap Bisnis', '2026-09-20 14:00:00', '2026-09-22 12:00:00', 'dikonfirmasi');

-- =====================================================
-- SELESAI
-- Catatan: password disimpan plain text (tanpa hash)
-- Login default -> username: admin | password: admin123
-- =====================================================
