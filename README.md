# 🚀 Laravel Xendit Payment Integration

Sistem integrasi *payment gateway* menggunakan Laravel 11 dan Xendit. Project ini menangani alur transaksi lengkap secara otomatis, mulai dari pembuatan invoice hingga pengiriman notifikasi email berdasarkan status pembayaran.

---

## ✨ Fitur Utama

* **🛒 Checkout System**: Terhubung langsung dengan Xendit Invoice API untuk pembuatan tagihan.
* **📡 Automated Webhooks**: Menangani callback dari Xendit untuk sinkronisasi status pembayaran secara real-time.
* **📧 Email Notifications**: 
    * **Success Mail**: Dikirim otomatis saat pembayaran berhasil (`PAID`).
    * **Failed/Expired Mail**: Dikirim otomatis jika batas waktu pembayaran habis (`EXPIRED`).
* **🔐 Callback Security**: Menggunakan verifikasi *Callback Token* untuk memastikan data hanya berasal dari server resmi Xendit.
* **👥 User-Order Mapping**: Pesanan terhubung otomatis dengan akun user yang sedang login.
* **📋 Order History**: Halaman riwayat pesanan yang terfilter (User hanya bisa melihat pesanan miliknya sendiri).

---

## 🛠️ Tech Stack

* **Framework**: [Laravel 11](https://laravel.com)
* **Payment Gateway**: [Xendit SDK](https://github.com/xendit/xendit-php)
* **Environment**: Laragon
* **Testing Tools**: 
    * **Ngrok**: Untuk mengekspos localhost agar bisa menerima Webhook.
    * **Mailpit/Mailcatcher**: Untuk menangkap dan mengetes pengiriman email di lokal.
    * **Postman**: Untuk simulasi webhook testing.

---

## ⚙️ Instalasi

### 1. Clone Project
```bash
git clone [https://github.com/username/laravel-xendit.git](https://github.com/username/laravel-xendit.git)
cd laravel-xendit

### 2. Install Dependencies

composer install
npm install && npm run dev

### 3. Konfigurasi Database & Environment

# Xendit Configuration
XENDIT_SECRET_KEY=xnd_development_...
XENDIT_CALLBACK_TOKEN=your_callback_token_from_xendit_dashboard

# Mail Configuration (Laragon/Mailpit)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null

### 4. Migrasi Database

php artisan migrate


Cara Mengetes Webhook (Local)