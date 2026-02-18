Laravel Xendit Payment Integration

Sistem integrasi payment gateway menggunakan Laravel 11 dan Xendit.
Project ini menangani alur transaksi lengkap secara otomatis, mulai dari pembuatan invoice, webhook callback, hingga notifikasi email berdasarkan status pembayaran.

✨ Fitur Utama

🛒 Checkout System: Terhubung langsung dengan API Invoice Xendit untuk pembuatan tagihan.

🔁 Automated Webhooks: Menangani callback dari Xendit untuk sinkronisasi status pembayaran secara real-time.

📧 Email Notifications:

Notifikasi email saat pembayaran berhasil (PAID).

Notifikasi email saat pembayaran gagal/habis waktu (EXPIRED).

🔐 Keamanan Callback: Verifikasi callback token untuk memastikan data berasal dari server Xendit.

👤 Order Terhubung dengan User: Pesanan otomatis terhubung dengan akun user yang sedang login.

📜 Riwayat Pesanan: Halaman riwayat pesanan yang terfilter (user hanya bisa melihat miliknya).

🛠️ Tech Stack

Framework: Laravel 11

Payment Gateway: Xendit SDK

Tools (Dev): Ngrok, Mailpit/Mailcatcher, Postman

⚙️ Instalasi
1. Clone Project
git clone https://github.com/RfndUsdan/learn-laravel-xendit.git
cd learn-laravel-xendit

2. Install Dependencies
composer install
npm install && npm run dev

3. Konfigurasi .env

Salin konfigurasi dari file contoh:

cp .env.example .env


Lalu atur variabel penting seperti:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_db
DB_PASSWORD=password_db

# Xendit
XENDIT_SECRET_KEY=xnd_development_...
XENDIT_CALLBACK_TOKEN=token_callback

# Mail (opsional)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025

4. Migrasi Database
php artisan migrate

5. Jalankan Aplikasi
php artisan serve

📦 Cara Kerja Webhook (Local)

Untuk menerima webhook dari Xendit saat pengembangan lokal, Anda bisa memakai Ngrok.

ngrok http 8000


Lalu gunakan URL https://...ngrok.io/payment/callback sebagai webhook di dashboard Xendit.

📍 Struktur Fitur (Opsional)

Repositori ini umumnya memiliki struktur kode Laravel standar (routes, controllers, models, migrations) yang menangani:

Pembuatan order & invoice

Callback webhook

Pengiriman email berdasarkan status pembayaran

🧪 Testing

Gunakan Postman atau tool lainnya untuk mengetes alur pembayaran dan webhook.

📚 Referensi

Xendit API docs – panduan penggunaan fitur payment gateway Xendit (Invoice, Callback