# Bengkel Management System

Aplikasi berbasis web Laravel untuk mengelola operasional harian bengkel motor, mulai dari pendaftaran (Service Advisor), pengelolaan work order, manajemen stok suku cadang, hingga pembuatan nota transaksi secara otomatis.

## Product Context
- **Problem:** Pengelolaan operasional bengkel secara manual rentan terhadap kesalahan pencatatan stok suku cadang dan kesulitan melacak riwayat servis konsumen.
- **Goals:** Mencatat seluruh data pendaftaran dan Work Order secara digital dengan format ID auto-generate yang akurat. Mengurangi kesalahan stok suku cadang melalui pemotongan otomatis (Ready for Sale dan Booked) saat transaksi berlangsung.

## Tech Stack
- Frontend: Blade Template, jQuery, AJAX, Bootstrap
- Backend: Laravel 10
- Database: MySQL
- Hosting: VPS / cPanel

## Key Features
- **Form Pendaftaran (Service Advisor):** Pencatatan keluhan konsumen dan data kendaraan.
- **Work Order Management:** Pembuatan, pemrosesan, dan penyelesaian work order oleh mekanik dan service advisor.
- **Manajemen Stok Suku Cadang:** Validasi dan pemotongan stok secara otomatis sesuai status transaksi.
- **Auto-Generate ID:** Pembuatan ID unik secara otomatis untuk setiap Form, Work Order, dan Nota Transaksi.
- **Integrasi API:** Mendukung pengiriman data transaksi ke sistem/server pusat melalui REST API.

## Prerequisites
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM

## Setup Instructions

1. Clone repository ini:
   ```bash
   git clone https://github.com/Biw-id/bengkel-management-test.git
   cd bengkel-management-test
   ```

2. Install dependencies backend dan frontend:
   ```bash
   composer install
   npm install
   ```

3. Salin `.env.example` ke `.env` dan konfigurasikan variabel environment Anda (termasuk koneksi database):
   ```bash
   cp .env.example .env
   ```

4. Generate app key:
   ```bash
   php artisan key:generate
   ```

5. Jalankan migrasi dan seed database:
   ```bash
   php artisan migrate --seed
   ```

6. Konfigurasikan Environment Variables tambahan (opsional) di `.env` jika menggunakan API pusat:
   ```env
   DB_DATABASE=your_database_name
   MAIN_DEALER_API_URL=https://api.example.com
   ```

7. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di `http://localhost:8000`.

## Developer Guidelines
- Patuhi konvensi error handling terpusat menggunakan format `{ "success": boolean, "message": string, "data"?: any }`.
- Logic bisnis hanya boleh diletakkan di layer **Services**.
- Semua entitas memiliki satu strategi ID terpusat menggunakan tipe yang konsisten.
- Gunakan tipe data `decimal(12,2)` atau `integer` dalam perhitungan uang untuk menghindari error *floating point*.

## License
Open Source - Dapat digunakan dan dimodifikasi untuk keperluan pengujian dan manajemen bengkel umum.
