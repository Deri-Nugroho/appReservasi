<?php
// =====================================================
// Konfigurasi Koneksi Database
// Sesuaikan host, username, password sesuai server kamu
// =====================================================

$DB_HOST = 'localhost';
$DB_NAME = 'db_reservasi_ruangan';
$DB_USER = 'root';       // ganti sesuai user MySQL/MariaDB kamu
$DB_PASS = '';           // ganti sesuai password MySQL/MariaDB kamu

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Koneksi database gagal: ' . $e->getMessage()
    ]));
}
