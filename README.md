# SIRDM HIMTI - Repositori Dokumentasi Multimedia

Sistem Informasi Repositori Dokumentasi Multimedia (SIRDM) adalah platform internal Himpunan Mahasiswa Teknik Informatika (HIMTI) UIN Syarif Hidayatullah Jakarta untuk menyimpan, mengelola, dan mendistribusikan dokumentasi acara dalam bentuk foto dan video (termasuk streaming).

## Persyaratan Sistem

Pastikan Anda telah menginstal perangkat lunak berikut di komputer server/lokal Anda:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (atau Docker Engine & Docker Compose)
- [Git](https://git-scm.com/)

## Cara Instalasi & Menjalankan Aplikasi

Aplikasi ini menggunakan Laravel Sail (Docker) sehingga sangat mudah dijalankan tanpa perlu menginstal PHP, MySQL, atau Redis secara manual di komputer Anda.

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/username/sirdm-himti.git
   cd sirdm-himti
   ```

2. **Salin file konfigurasi environment:**
   ```bash
   cp .env.example .env
   ```
   *(File `.env` akan menggunakan konfigurasi database MySQL bawaan Docker).*

3. **Instal dependensi Composer:**
   Jika Anda belum memiliki PHP/Composer di komputer, Anda dapat menggunakan kontainer Docker sementara untuk menginstalnya:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php83-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Jalankan Docker / Laravel Sail:**
   ```bash
   ./vendor/bin/sail up -d
   ```
   *(Tunggu beberapa saat hingga kontainer MySQL, Redis, dan Web menyala).*

5. **Generate Application Key:**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Instal NPM Modules & Build Assets:**
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

7. **Migrasi Database & Seeding (Penting):**
   Ini akan membuat tabel database beserta akun Super Admin bawaan.
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

8. **Buat Symlink Storage:**
   Agar gambar/video bisa diakses:
   ```bash
   ./vendor/bin/sail artisan storage:link
   ```

Aplikasi sekarang dapat diakses di browser melalui: **http://localhost**

## Akun Login Default

Setelah melakukan `migrate --seed`, Anda dapat masuk menggunakan kredensial berikut:

- **Super Admin** (Akses Penuh + Pengaturan)
  - Username: `super_admin`
  - Password: `password`

- **Admin PDD** (Hanya Kelola Album Sendiri)
  - Username: `admin_pdd`
  - Password: `password`

*Pastikan untuk segera mengubah password Super Admin setelah aplikasi di-deploy ke server produksi!*

## Penanganan Masalah (Troubleshooting)

- **SQLSTATE[HY000] [2002] Connection refused:** 
  MySQL di dalam Docker butuh waktu sekitar 10-15 detik untuk menyala penuh saat pertama kali. Tunggu sebentar lalu jalankan ulang `sail artisan migrate`.
- **Permission Denied / Akses File Ditolak (Linux/Mac):**
  Jalankan perintah ini agar folder storage bisa ditulis oleh server:
  ```bash
  sudo chown -R $USER:www-data storage bootstrap/cache
  sudo chmod -R 775 storage bootstrap/cache
  ```

## Tech Stack
- Backend: Laravel 11 (PHP 8.3)
- Frontend: Blade, Alpine.js, Vanilla CSS
- Database: MySQL
- Cache/Queue: Redis
- Environment: Docker (Laravel Sail)
