# Sistem Reservasi Ruangan (Hotel / Meeting Room)

Aplikasi CRUD sederhana untuk mengelola data ruangan dan reservasi/booking, dibangun dengan:

- **PHP** (native, PDO)
- **MySQL / MariaDB**
- **Bootstrap 5**
- **AJAX (jQuery)**

## Fitur

- Dashboard ringkasan (jumlah ruangan, ruangan tersedia, booking hari ini, booking pending)
- CRUD Data Ruangan (tambah, edit, hapus, lihat) — tanpa reload halaman
- CRUD Data Reservasi (tambah, edit, hapus, lihat) — tanpa reload halaman
- Validasi otomatis **cek bentrok jadwal**: sistem akan menolak booking baru/edit jika rentang waktu bertabrakan dengan booking lain pada ruangan yang sama
- Filter reservasi berdasarkan status (pending, dikonfirmasi, dibatalkan, selesai)

## Struktur Folder

```
reservasi_app/
├── ajax/
│   ├── rooms_ajax.php       # Handler AJAX CRUD ruangan
│   └── bookings_ajax.php    # Handler AJAX CRUD booking + cek bentrok
├── config/
│   └── database.php         # Koneksi PDO ke database
├── includes/
│   ├── header.php           # Navbar & head HTML (Bootstrap)
│   └── footer.php           # Footer & script bersama
├── index.php                # Dashboard
├── rooms.php                # Halaman data ruangan
├── bookings.php              # Halaman data reservasi
└── reservasi_ruangan.sql    # Script SQL (schema + data awal)
```

## Cara Instalasi

### Opsi A: Instalasi Manual (XAMPP/Laragon/Local Server)

#### 1. Siapkan Database

Import file `reservasi_ruangan.sql` ke server MySQL/MariaDB kamu:

**Lewat phpMyAdmin:**
1. Buat tab baru → menu **Import**
2. Pilih file `reservasi_ruangan.sql`
3. Klik **Go / Kirim**

**Lewat terminal / CLI:**
```bash
mysql -u root -p < reservasi_ruangan.sql
```

File ini akan otomatis:
- Membuat database `db_reservasi_ruangan`
- Membuat tabel `users`, `rooms`, `bookings`
- Mengisi beberapa data contoh (5 ruangan, 3 booking, 1 user admin)

> **Catatan:** Password pada tabel `users` disimpan dalam bentuk **plain text** (tanpa hash), sesuai permintaan — cocok untuk keperluan belajar/demo. Untuk aplikasi produksi, sangat disarankan menggunakan hashing (`password_hash()` / `password_verify()`).

Login default:
- Username: `admin`
- Password: `admin123`

### 2. Konfigurasi Koneksi Database

Buka file `config/database.php`, sesuaikan dengan kredensial server database kamu:

```php
$DB_HOST = 'localhost';
$DB_NAME = 'db_reservasi_ruangan';
$DB_USER = 'root';   // ganti sesuai user MySQL/MariaDB kamu
$DB_PASS = '';       // ganti sesuai password MySQL/MariaDB kamu
```

### 3. Jalankan Aplikasi

- Letakkan folder `reservasi_app` ke dalam direktori web server (misal `htdocs` untuk XAMPP, atau `www` untuk Laragon)
- Akses melalui browser, contoh: `http://localhost/reservasi_app/`

### Opsi B: Deploy dengan Docker Compose (Production/Cloud)

Untuk deployment di server cloud (AWS EC2, dll) menggunakan Docker Compose, ikuti panduan lengkap di:

📖 **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)**

Ringkasan cepat:

```bash
# 1. Install Docker dan Docker Compose
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# 2. Clone repository
git clone https://github.com/Deri-Nugroho/appReservasi.git
cd appReservasi

# 3. Jalankan dengan Docker Compose
docker-compose up -d --build

# 4. Akses aplikasi di http://<IP_SERVER>
```

**Komponen Docker:**
- **Web Server**: PHP 8.2 + Apache
- **Database**: MariaDB 11 (Jammy)
- **Database** akan otomatis di-import dari file `reservasi_ruangan.sql`

## Alur Penggunaan

1. **Dashboard** — melihat ringkasan statistik dan reservasi terbaru
2. **Data Ruangan** — tambah ruangan baru (nama, tipe, kapasitas, harga per jam, fasilitas, status)
3. **Reservasi** — buat booking dengan memilih ruangan dan rentang waktu; sistem otomatis mengecek apakah jadwal bentrok sebelum data disimpan

## Catatan Pengembangan Lanjutan (Opsional)

Beberapa hal yang bisa ditambahkan sendiri sesuai kebutuhan:
- Halaman login berbasis tabel `users` (session-based auth)
- Tampilan kalender visual (misal pakai FullCalendar.js) untuk jadwal booking per ruangan
- Export data reservasi ke Excel/PDF
- Upload gambar ruangan (kolom `gambar` sudah tersedia di tabel `rooms`)
- Notifikasi email saat status booking berubah

## Teknologi

| Komponen   | Versi/Sumber                                   |
|------------|-------------------------------------------------|
| Bootstrap  | 5.3.3 (CDN)                                     |
| Bootstrap Icons | 1.11.3 (CDN)                              |
| jQuery     | 3.7.1 (CDN)                                     |
| PHP        | 7.4+ / 8.x (disarankan)                        |
| Database   | MySQL 5.7+ / MariaDB 10.x                      |
