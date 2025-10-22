# Library Booking App 

Aplikasi web untuk booking ruangan perpustakaan, dibuat pakai PHP dengan arsitektur MVC. Ini adalah bagian dari project **PBL (Project-Based Learning)** di **Teknik Informatika – Politeknik Negeri Jakarta**.


---

## Fitur sekarang:
- Register (pakai NIM untuk Mahasiswa, NIP untuk Dosen)
- Verifikasi email pakai OTP
- Login (bisa pakai Email/NIM/NIP)
- Logout
- Lupa password & reset password
- CSRF protection, password hashing, input validation
- Middleware (Auth, Admin, Guest) di `App\Core\Middleware`
- Email service & logging di `App\Core\Services`

---

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
```

**Buat Gmail App Password:**  
Buka [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords), aktifin 2-Step Verification dulu, terus bikin app password buat SMTP.

### 3. Bikin Database & Jalanin Migration

```sql
CREATE DATABASE library_booking_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php migration.php
php seed.php
```

### 4. Jalanin Server

```bash
php -S localhost:8000 -t public
```

Buka browser, akses **http://localhost:8000**

### 5. Login

**Admin:** `admin@pnj.ac.id` / `admin123`  
**Mahasiswa:** `mahasiswa@stu.pnj.ac.id` / `test1234`  
**Dosen:** `dosen@tik.pnj.ac.id` / `test1234`

---

===== Running TailwindCSS =====
npm run dev


