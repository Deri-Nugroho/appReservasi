# 🚀 Panduan Deploy App Reservasi dengan Docker Compose
## Langkah demi Langkah - Ikuti Urutan Tepat!

---

## 📋 PRASYARAT

Sebelum mulai, pastikan Anda memiliki:
- ✅ Server AWS EC2 dengan Ubuntu 24.04
- ✅ Akses SSH ke server
- ✅ Repository: https://github.com/Deri-Nugroho/appReservasi

---

## 🔧 LANGKAH 1: Instalasi Docker dan Docker Compose

Jalankan perintah berikut **SEKARANG** di server EC2 Anda:

```bash
# 1. Update system
sudo apt update

# 2. Install dependencies
sudo apt install -y ca-certificates curl gnupg lsb-release

# 3. Add Docker's official GPG key
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# 4. Add Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# 5. Update package index
sudo apt update

# 6. Install Docker
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# 7. Enable dan start Docker
sudo systemctl enable docker
sudo systemctl start docker

# 8. Verifikasi instalasi Docker
docker --version
```

**Output yang diharapkan:** `Docker version 29.8.2, build ...`

---

## 🔧 LANGKAH 2: Instalasi Docker Compose Standalone

```bash
# 1. Download Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

# 2. Berikan permission execute
sudo chmod +x /usr/local/bin/docker-compose

# 3. Verifikasi instalasi
docker-compose --version
```

**Output yang diharapkan:** `Docker Compose version v5.6.0`

---

## 👤 LANGKAH 3: Tambah User ke Docker Group (WAJIB!)

```bash
# 1. Tambah user ke docker group
sudo usermod -aG docker $USER

# 2. Aktivasi group (PENTING - jangan skip!)
newgrp docker

# 3. Verifikasi (harus bisa jalan tanpa sudo)
docker ps
```

**Output yang diharapkan:** List kosong atau list container yang sedang berjalan (tanpa error permission denied)

---

## 📥 LANGKAH 4: Clone Repository

```bash
# 1. Install git jika belum ada
sudo apt install -y git

# 2. Clone repository
cd ~
git clone https://github.com/Deri-Nugroho/appReservasi.git

# 3. Masuk ke direktori aplikasi
cd appReservasi

# 4. Verifikasi file ada
ls -la
```

**File yang harus ada:**
- docker-compose.yml
- Dockerfile
- reservasi_ruangan.sql
- config/database.php
- ajax/
- includes/
- index.php, rooms.php, bookings.php

---

## 🚀 LANGKAH 5: Jalankan Docker Compose

```bash
# 1. Build dan jalankan semua container
docker-compose up -d --build
```

**Proses ini akan:**
- Pull image MariaDB 11 dari Docker Hub
- Build image PHP 8.2 + Apache dari Dockerfile
- Import database dari file reservasi_ruangan.sql (OTOMATIS!)
- Jalankan 2 container: dbserver dan webserver

**Tunggu sampai selesai (sekitar 1-2 menit)**

---

## ✅ LANGKAH 6: Verifikasi Deployment

```bash
# 1. Cek status container
docker-compose ps
```

**Output yang diharapkan:**
```
NAME               IMAGE                    STATUS                  PORTS
appreservasi_db    mariadb:11-jammy         Up X seconds            3306/tcp
appreservasi_web   appreservasi-webserver   Up X seconds            0.0.0.0:80->80/tcp
```

**Status harus "Up" untuk kedua container!**

```bash
# 2. Cek logs webserver
docker-compose logs webserver
```

**Output yang diharapkan:** `Apache/2.4.68 configured -- resuming normal operations`

```bash
# 3. Cek logs database
docker-compose logs dbserver
```

**Output yang diharapkan:** `MariaDB init process done. Ready for start up.`

```bash
# 4. Cek tabel database
docker exec -it appreservasi_db mariadb -u reservasi_user -preservasi_password db_reservasi_ruangan -e "SHOW TABLES;"
```

**Output yang diharapkan:**
```
+--------------------------------+
| Tables_in_db_reservasi_ruangan |
+--------------------------------+
| bookings                       |
| rooms                          |
| users                          |
+--------------------------------+
```

---

## 🌐 LANGKAH 7: Konfigurasi Firewall

```bash
# 1. Allow HTTP (port 80)
sudo ufw allow 80/tcp

# 2. Allow SSH (port 22)
sudo ufw allow 22/tcp

# 3. Enable firewall
sudo ufw enable

# 4. Cek status
sudo ufw status
```

**PENTING:** Pastikan Security Group AWS juga mengizinkan inbound traffic port 80 dari anywhere (0.0.0.0/0)

---

## 📡 LANGKAH 8: Dapatkan IP Public EC2

```bash
# Cek IP public
curl ifconfig.me
```

**Catat IP ini!** Misal: `100.59.194.211`

---

## 🎯 LANGKAH 9: Akses Aplikasi

Buka browser dan akses:
```
http://<IP_PUBLIC_EC2>
```

Contoh: `http://100.59.194.211`

**Login Default:**
- Username: `admin`
- Password: `admin123`

---

## 🛠️ PERINTAH-PERINTAH PENTING

### Cek Status Container
```bash
docker-compose ps
```

### Lihat Logs
```bash
# Semua logs
docker-compose logs

# Logs webserver saja
docker-compose logs webserver

# Logs database saja
docker-compose logs dbserver
```

### Stop Container
```bash
docker-compose stop
```

### Start Container
```bash
docker-compose start
```

### Restart Container
```bash
docker-compose restart
```

### Stop dan Hapus Container
```bash
docker-compose down
```

### Stop dan Hapus Container + Volume Data
```bash
docker-compose down -v
```

### Build Ulang dan Restart
```bash
docker-compose down
docker-compose up -d --build
```

---

## 🐛 TROUBLESHOOTING

### Masalah 1: "permission denied while trying to connect to the docker API"

**Solusi:**
```bash
sudo usermod -aG docker $USER
newgrp docker
```

### Masalah 2: Container tidak mau start

**Solusi:**
```bash
# Cek logs
docker-compose logs

# Cek jika port 80 sudah digunakan
sudo lsof -i :80

# Stop service yang menggunakan port 80
sudo systemctl stop nginx
sudo systemctl stop apache2

# Restart Docker Compose
docker-compose down
docker-compose up -d
```

### Masalah 3: Database connection failed

**Solusi:**
```bash
# Cek apakah container database berjalan
docker-compose ps

# Cek logs database
docker-compose logs dbserver

# Restart web server
docker-compose restart webserver
```

### Masalah 4: Tidak bisa akses dari browser

**Solusi:**
```bash
# 1. Cek firewall
sudo ufw status

# 2. Allow port 80
sudo ufw allow 80/tcp

# 3. Cek Security Group AWS
# Pastikan inbound rule port 80 diizinkan dari 0.0.0.0/0

# 4. Cek container webserver
docker-compose ps
docker-compose logs webserver
```

### Masalah 5: Database tidak ter-import

**Solusi:**
```bash
# Import manual ke container database
docker exec -i appreservasi_db mariadb -u reservasi_user -preservasi_password db_reservasi_ruangan < reservasi_ruangan.sql

# Verifikasi
docker exec -it appreservasi_db mariadb -u reservasi_user -preservasi_password db_reservasi_ruangan -e "SHOW TABLES;"
```

---

## 📝 KONFIGURASI DATABASE

**File:** `docker-compose.yml`

```yaml
environment:
  MYSQL_ROOT_PASSWORD: root_password      # Ganti untuk production
  MYSQL_DATABASE: db_reservasi_ruangan    # Nama database
  MYSQL_USER: reservasi_user              # User database
  MYSQL_PASSWORD: reservasi_password      # Password database
```

**Untuk mengubah:**
1. Edit file `docker-compose.yml`
2. Jalankan `docker-compose down`
3. Jalankan `docker-compose up -d`

---

## 🔄 UPDATE APLIKASI

Jika ada perubahan di repository:

```bash
# 1. Pull latest changes
cd ~/appReservasi
git pull origin main

# 2. Rebuild dan restart
docker-compose down
docker-compose up -d --build

# 3. Cek status
docker-compose ps
```

---

## 🗑️ CLEANUP - Hapus Semua

Untuk menghapus seluruh deployment:

```bash
# Stop dan hapus container
docker-compose down

# Hapus volume (hapus data database)
docker-compose down -v

# Hapus images
docker rmi $(docker images -q appreservasi-webserver)
```

---

## ✅ CHECKLIST DEPLOYMENT

Sebelum menganggap selesai, pastikan:

- [ ] Docker dan Docker Compose terinstall
- [ ] User sudah di docker group
- [ ] Repository berhasil di-clone
- [ ] `docker-compose ps` menunjukkan kedua container "Up"
- [ ] Logs webserver menunjukkan Apache berjalan
- [ ] Logs database menunjukkan "ready for connections"
- [ ] Tabel database (users, rooms, bookings) ada
- [ ] Firewall mengizinkan port 80
- [ ] Security Group AWS mengizinkan port 80
- [ ] Aplikasi dapat diakses via browser
- [ ] Login berhasil dengan admin/admin123

---

## 📚 INFORMASI PENTING

### URL Aplikasi
- **HTTP:** `http://<IP_PUBLIC_EC2>`
- **Contoh:** `http://100.59.194.211`

### Login Default
- **Username:** `admin`
- **Password:** `admin123`

### Port yang Digunakan
- **HTTP:** 80 (exposed ke luar)
- **MySQL:** 3306 (internal, tidak exposed)

### Volume Persistence
- Database data disimpan di Docker volume `db_data`
- Aplikasi files disimpan di direktori lokal (mounted ke container)

### Struktur Container
1. **appreservasi_db** - MariaDB 11
2. **appreservasi_web** - PHP 8.2 + Apache

---

## 🎉 SELESAI!

Aplikasi Sistem Reservasi Ruangan sudah berhasil di-deploy dengan Docker Compose!

**Jika ada masalah:**
1. Cek logs: `docker-compose logs`
2. Verifikasi status: `docker-compose ps`
3. Review troubleshooting di atas

**Dokumentasi tambahan:**
- `DEPLOYMENT_GUIDE.md` - Panduan detail teknis
- `README.md` - Dokumentasi aplikasi

---

**Selamat Menggunakan! 🚀**
