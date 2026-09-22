# Panduan Deploy Admin Panel Gagan Suganda (cPanel + MySQL)

Dokumentasi ini menjelaskan langkah-langkah mempublikasikan Admin Panel Portofolio Anda ke hosting cPanel (seperti RumahWeb atau layanan shared hosting lainnya).

---

## 🚀 Langkah-Langkah Deploy

### 1. Persiapan Database MySQL
1. Masuk ke **cPanel** Anda.
2. Cari dan klik menu **MySQL® Database Wizard** atau **Databases**.
3. Buat database baru (misalnya: `gagan_profile_db`).
4. Buat user database baru dan hubungkan ke database tersebut dengan mencentang pilihan **ALL PRIVILEGES**.
5. Masuk ke **phpMyAdmin** melalui cPanel, pilih database Anda, lalu klik tab **Import**. Pilih file `sql/schema.sql` untuk mengimpor skema tabel berserta dengan akun administrator awal.

### 2. Unggah Berkas ke cPanel
1. Kompres seluruh isi folder proyek `my-profile-release-v1.0.0` Anda ke dalam format `.zip` (kecuali folder `node_modules` jika ada, dan file `.env` lokal Anda).
2. Di cPanel, buka **File Manager** dan masuk ke direktori `public_html/` (atau direktori root domain/subdomain pilihan Anda).
3. Klik **Upload** dan unggah berkas `.zip` yang sudah dikompres.
4. Setelah selesai diunggah, klik kanan berkas `.zip` tersebut di File Manager cPanel, lalu pilih **Extract**.

### 3. Konfigurasi Environment (`.env`)
1. Salin file `.env.example` menjadi `.env` di direktori root domain Anda menggunakan editor File Manager cPanel.
2. Atur kredensial database MySQL hosting Anda serta tautan web aktual:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=nama_database_cpanel_anda
   DB_USER=nama_user_database_cpanel_anda
   DB_PASS=kata_sandi_user_database_anda
   JWT_SECRET=buat_kunci_rahasia_yang_sangat_panjang_dan_rumit_di_sini_bebas_acak
   APP_URL=https://domain-anda.com
   ADMIN_EMAIL=gaganbaonkk@gmail.com
   ```

### 4. Instalasi Dependensi Server (Composer)
- **Opsi A (Melalui Terminal SSH cPanel):**
  Jika hosting Anda mendukung SSH/Terminal, buka Terminal di cPanel dan jalankan perintah:
  ```bash
  composer install --no-dev
  ```

- **Opsi B (Upload manual vendor):**
  Jika Anda tidak memiliki akses SSH, pastikan folder `vendor/` di lokal komputer Anda sudah ikut dikompres dan diunggah ke hosting bersamaan dengan file lainnya.

### 5. Atur Hak Akses Folder Gambar
Pastikan folder `uploads/` yang baru diekstrak memiliki hak akses yang dapat ditulis (writable) oleh server. Minimal berikan permission **755** atau **775** melalui klik kanan di File Manager cPanel -> **Change Permissions**.

---

## 🔒 Hardening & Keamanan Direktori
Untuk mencegah peretasan ataupun pencurian file berkas konfigurasi sensitif:
- Keamanan direktori dasar seperti folder `/app`, berkas `.env`, `/sql`, dan dependensi `/vendor` telah dilindungi otomatis dari akses luar menggunakan aturan **`.htaccess`** yang terpasang di direktori utama.
- Eksekusi skrip script berbahaya (PHP bypass backdoor) di dalam folder `/uploads` sudah ditangkal otomatis menggunakan **`uploads/.htaccess`** yang disertakan.

---

## 🔑 Detail Login Default
- **Halaman Admin:** `https://domain-anda.com/admin/login.php`
- **Email Default:** `gaganbaonkk@gmail.com`
- **Password Default:** `admin` (Segera ubah kata sandi Anda melalui menu **Pengaturan** setelah berhasil login pertamakali!)
