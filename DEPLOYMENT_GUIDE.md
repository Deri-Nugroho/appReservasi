# Panduan Deploy App Reservasi dengan Docker Compose di AWS EC2

## 📋 Deskripsi

Dokumen ini berisi panduan lengkap untuk deploy aplikasi Sistem Reservasi Ruangan menggunakan Docker Compose di server AWS EC2 (Ubuntu 24.04).

Aplikasi ini menggunakan:
- **Web Server**: Apache + PHP 8.2
- **Database**: MariaDB 11 (Jammy)
- **Orchestration**: Docker Compose

---

## 🛠️ Prasyarat

Sebelum memulai, pastikan Anda memiliki:
1. Akses SSH ke server AWS EC2 (Ubuntu 24.04)
2. User dengan akses sudo (default: ubuntu)
3. Repository GitHub: https://github.com/Deri-Nugroho/appReservasi

---

## 📦 Langkah 1: Instalasi Docker dan Docker Compose

### 1.1 Update System

```bash
sudo apt update
sudo apt upgrade -y
```

### 1.2 Instalasi Docker

```bash
# Install dependencies
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Update package index
sudo apt update

# Install Docker
sudo apt install -y docker-ce docker-ce-cli containerd.io

# Enable dan start Docker
sudo systemctl enable docker
sudo systemctl start docker

# Verifikasi instalasi
docker --version
```

### 1.3 Instalasi Docker Compose

```bash
# Download Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# Berikan permission execute
sudo chmod +x /usr/local/bin/docker-compose

# Verifikasi instalasi
docker-compose --version
```

### 1.4 (Opsional) Tambah User ke Docker Group

Agar tidak perlu menggunakan sudo setiap kali menjalankan docker:

```bash
sudo usermod -aG docker $USER
newgrp docker
```

---

## 📥 Langkah 2: Clone Repository

```bash
# Install git jika belum ada
sudo apt install -y git

# Clone repository
git clone https://github.com/Deri-Nugroho/appReservasi.git

# Masuk ke direktori aplikasi
cd appReservasi
```

---

## 🚀 Langkah 3: Deploy dengan Docker Compose

### 3.1 Review Konfigurasi

Sebelum menjalankan, Anda dapat mengubah konfigurasi di file `docker-compose.yml` jika diperlukan:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: root_password      # Ganti dengan password root yang aman
  MYSQL_DATABASE: db_reservasi_ruangan    # Nama database
  MYSQL_USER: reservasi_user              # User database
  MYSQL_PASSWORD: reservasi_password      # Password database
```

### 3.2 Build dan Jalankan Container

```bash
# Build dan jalankan semua service
docker-compose up -d --build
```

Penjelasan:
- `-d`: Run di background (detached mode)
- `--build`: Build image dari Dockerfile sebelum menjalankan

Proses ini akan:
1. Build image PHP dengan Apache berdasarkan Dockerfile
2. Pull image MariaDB 11 dari Docker Hub
3. Import database dari file `reservasi_ruangan.sql`
4. Jalankan kedua container dengan network yang sama

### 3.3 Cek Status Container

```bash
# Lihat status semua container
docker-compose ps

# Output yang diharapkan:
# NAME                STATUS              PORTS
# appreservasi_db     Up (healthy)        3306/tcp
# appreservasi_web    Up                  0.0.0.0:80->80/tcp
```

### 3.4 Cek Logs

```bash
# Lihat logs dari semua service
docker-compose logs

# Lihat logs web server saja
docker-compose logs webserver

# Lihat logs database saja
docker-compose logs dbserver
```

---

## 🔍 Langkah 4: Verifikasi Deploy

### 4.1 Cek Database

Database akan otomatis di-import dari file `reservasi_ruangan.sql` saat container pertama kali dijalankan.

```bash
# Masuk ke container database
docker exec -it appreservasi_db mysql -u reservasi_user -preservasi_password db_reservasi_ruangan

# Di dalam MySQL prompt, jalankan:
SHOW TABLES;
SELECT * FROM users;
SELECT * FROM rooms;
exit;
```

### 4.2 Akses Aplikasi

Buka browser dan akses:
- **Jika menggunakan IP EC2**: `http://<PUBLIC_IP_EC2>`
- **Jika menggunakan domain**: `http://<DOMAIN_ANDA>`

Login default:
- **Username**: `admin`
- **Password**: `admin123`

---

## 🔧 Perintah Docker Compose Umum

### Melihat Container yang Berjalan

```bash
docker-compose ps
```

### Stop Semua Container

```bash
docker-compose stop
```

### Start Semua Container

```bash
docker-compose start
```

### Restart Semua Container

```bash
docker-compose restart
```

### Stop dan Hapus Container

```bash
docker-compose down
```

### Stop dan Hapus Container + Volume (Data akan hilang!)

```bash
docker-compose down -v
```

### Rebuild dan Restart

```bash
docker-compose up -d --build
```

---

## 📊 Struktur Container

Docker Compose akan membuat 2 container:

1. **appreservasi_db** (MariaDB 11)
   - Port: 3306 (internal)
   - Volume: db_data (persistent storage)
   - Environment variables untuk database credentials

2. **appreservasi_web** (PHP 8.2 + Apache)
   - Port: 80 (exposed ke host)
   - Mount volume: direktori aplikasi ke `/var/www/html`
   - PHP extensions: pdo, pdo_mysql, mysqli

---

## 🔐 Security Best Practices

### 1. Ganti Password Default

Edit file `docker-compose.yml` dan ganti:
- `MYSQL_ROOT_PASSWORD`
- `MYSQL_PASSWORD`

### 2. Gunakan Firewall

```bash
# Allow HTTP
sudo ufw allow 80/tcp

# Allow SSH
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

### 3. (Opsional) Gunakan SSL dengan Let's Encrypt

Instal Certbot dan konfigurasi SSL untuk HTTPS.

---

## 🐛 Troubleshooting

### Container tidak mau start

```bash
# Cek logs
docker-compose logs

# Cek jika port 80 sudah digunakan
sudo lsof -i :80

# Stop service yang menggunakan port 80 (misal nginx/apache)
sudo systemctl stop nginx
sudo systemctl stop apache2
```

### Database connection failed

```bash
# Cek apakah container database sudah healthy
docker-compose ps

# Cek logs database
docker-compose logs dbserver

# Tunggu beberapa detik dan restart web server
docker-compose restart webserver
```

### SQL tidak ter-import otomatis

```bash
# Import manual ke container database
docker exec -i appreservasi_db mysql -u reservasi_user -preservasi_password db_reservasi_ruangan < reservasi_ruangan.sql
```

### Permission denied pada volume

```bash
# Fix permission
sudo chown -R $USER:$USER .
sudo chmod -R 755 .
```

### PHP extensions tidak terinstall

Build ulang container web:

```bash
docker-compose down
docker-compose up -d --build
```

---

## 📝 Update Aplikasi

Jika ada perubahan di repository:

```bash
# Pull latest changes
git pull origin main

# Rebuild dan restart
docker-compose up -d --build
```

---

## 🗑️ Cleanup (Hapus Semua)

Untuk menghapus seluruh deployment:

```bash
# Stop dan hapus container
docker-compose down

# Hapus volume (hapus data database)
docker-compose down -v

# Hapus images
docker rmi $(docker images -q appreservasi_web)
```

---

## 📚 Informasi Tambahan

### Login Default
- Username: `admin`
- Password: `admin123`

### Port yang Digunakan
- **HTTP**: 80
- **MySQL**: 3306 (internal, tidak exposed ke luar)

### Volume Persistence
- Database data disimpan di Docker volume `db_data`
- Aplikasi files disimpan di direktori lokal (mounted ke container)

---

## ✅ Checklist Deploy

- [ ] Docker dan Docker Compose terinstall
- [ ] Repository berhasil di-clone
- [ ] `docker-compose.yml` sudah dikonfigurasi
- [ ] Container berhasil di-build dan start
- [ ] Database ter-import otomatis
- [ ] Aplikasi dapat diakses via browser
- [ ] Login berhasil dengan kredensial default
- [ ] Firewall dikonfigurasi (port 80 dan 22)

---

## 📞 Support

Jika mengalami masalah:
1. Cek logs: `docker-compose logs`
2. Verifikasi status: `docker-compose ps`
3. Review konfigurasi di `docker-compose.yml`
4. Pastikan tidak ada konflik port

---

**Selamat Menggunakan! 🎉**
