# Library Booking App 

## Cara Jalanin Aplikasi

Yang dibutuhin:
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Ekstensi PHP: `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`

### 1. Clone & Install

```bash
git clone https://github.com/MohammadRizkiSyahputra/library-booking-app.git
cd library-booking-app
composer install
```

### 2. Setup Environment

```bash
cp .env.example .env
```

Buka file `.env`, isi konfigurasi database & email:
```ini
# Application
APP_NAME = "Library Booking App"
APP_ENV = development
APP_DEBUG = true
APP_TIMEZONE = Asia/Jakarta
SESSION_LIFETIME = 7200


# Database
DB_HOST = localhost
DB_PORT = 3306
DB_NAME = library_booking_app
DB_USER = root
DB_PASS = 

# Email (Gmail)
MAIL_HOST = smtp.gmail.com
MAIL_PORT = 587
MAIL_USERNAME = yourgmail@gmail.com
MAIL_PASSWORD = your_gmail_app_password
MAIL_ENCRYPTION = tls
MAIL_FROM_ADDRESS = yourgmail@gmail.com
MAIL_FROM_NAME = "Library Booking App"

# TURNSTILE
TURNSTILE_SITE=your_site_key_here
TURNSTILE_SECRET=your_secret_key_here
```

**Buat Gmail App Password:**  
Buka [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords), aktifin 2-Step Verification dulu, terus bikin app password buat SMTP.

**Buat Turnstile**  
Buka [https://www.cloudflare.com/application-services/products/turnstile/]

### 3. Bikin Database, Jalanin Aplikasi

```sql
CREATE DATABASE library_booking_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php Migration.php

php Seed.php

npm install

npm run dev

cd C:\xampp\htdocs\PBL\library-booking-app
.\Mailpit\mailpit.exe --smtp :1025 --listen :8025

php -S localhost:8000 -t public
```

```bash
php rollback.php -> jika mau ngerollback migrasi
```

Buka browser, akses **http://localhost:8000**

### 4. Login

**Admin:** `admin@pnj.ac.id` / `admin`  
**Mahasiswa:** `mahasiswa@stu.pnj.ac.id` / `mahasiswa`  
**Dosen:** `dosen@pnj.ac.id` / `dosen`

---



