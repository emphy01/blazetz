# BLAZT – Sneaker E-Commerce

Toko sneaker online kolaborasi Nike, Adidas, dan Puma.  
Dibangun dengan **PHP 8 Native + PDO + MySQL**.

---

## Stack

- **Back-end**: PHP 8.2 + PDO (MariaDB / MySQL)
- **Front-end**: Vanilla HTML + CSS + JavaScript
- **Deployment**: Railway (MySQL plugin) via GitHub repo

---

## Setup Lokal

### Prasyarat
- PHP 8.0+ (dengan ekstensi `pdo_mysql`, `mbstring`)
- MySQL / MariaDB
- Apache + `mod_rewrite` — disarankan gunakan **XAMPP** atau **Laragon**

### Langkah-langkah

```bash
# 1. Clone repo
git clone https://github.com/<username>/blazt.git
cd blazt

# 2. Buat config.php dari template
cp config.php config.real.php
# Lalu edit config.real.php → isi DB_HOST, DB_NAME, DB_USER, DB_PASS
# Rename: config.real.php → config.php  (jangan di-commit!)

# 3. Buat database & import skema
mysql -u root -p < db/schema.sql   # atau import via phpMyAdmin

# 4. Jalankan server lokal
php -S localhost:8000 -t .
```

Buka `http://localhost:8000/index.html`

---

## Deployment ke Railway

### Persiapan sekali-satu

1. **Push repo ke GitHub** (pastikan `config.php` ter-ignore oleh `.gitignore`)
2. Buka [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo**
3. Pilih repo BLAZT

### Tambahkan MySQL di Railway

1. Di dashboard Railway project → klik **+ New** → pilih **Database → MySQL**
2. Railway akan otomatis membuat env vars:
   - `MYSQLHOST`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLPORT`
3. Import skema database:  
   Buka tab **MySQL → Connect → Query** atau gunakan client lokal (TablePlus / DBeaver) dengan credentials dari Railway, lalu jalankan `if0_40894737_blazt_ecommerce.sql`

### Tambahkan Environment Variables

Di Railway project → tab **Variables**, tambahkan:

| Key | Value |
|-----|-------|
| `APP_ENV` | `production` |

> Variable `MYSQL*` sudah otomatis tersedia dari plugin MySQL Railway.

### Deploy

Setiap `git push` ke branch `main` akan otomatis men-trigger deploy baru di Railway.  
Railway membaca konfigurasi build dari [`nixpacks.toml`](nixpacks.toml).

---

## Struktur File

| File | Keterangan |
|------|------------|
| `config.php` | Template konfigurasi (DB + env constants) — **jangan commit versi asli** |
| `db.php` | Koneksi PDO, env-aware error logging |
| `functions.php` | Session aman (httponly, samesite), CSRF helper |
| `auth.php` | Guard redirect untuk halaman yg butuh login |
| `authenticate.php` | Login POST + rate-limiting |
| `register.php` | Form & handler registrasi |
| `cart_update.php` | AJAX endpoint keranjang (wajib CSRF token) |
| `cart_functions.php` | Logika cart & order |
| `.htaccess` | Security headers, gzip, proteksi file sensitif |
| `nixpacks.toml` | Konfigurasi build PHP untuk Railway |
| `.gitignore` | File yang tidak di-commit (secrets, logs, dumps) |
| `robots.txt` | Aturan crawler |
| `sitemap.xml` | Sitemap halaman publik |

---

## Catatan Keamanan

| File | Tindakan |
|------|---------|
| `config.php` | Ada di `.gitignore` — **jangan di-commit** |
| `seed_*.php` | Ada di `.gitignore` — hapus dari server setelah DB diisi |
| `*.sql` | Ada di `.gitignore` — import manual via Railway Query atau client |
| `logs/` | PHP error log — diblokir dari akses publik via `.htaccess` |

---

## Lisensi

© 2024 BLAZT. All Rights Reserved.
