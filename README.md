# 🌌 Galaksian - Backend Jastip Jepang–Indonesia

Galaksian adalah platform e-commerce dan Jastip (Jasa Titip) mobile-web antara Indonesia dan Jepang dengan fitur utama:
- Otentikasi OTP nomor handphone (WhatsApp/SMS).
- Model Belanja Jastip berbasis **Trip Aktif**.
- **Checkout 2 Tahap**:
  1. *Invoice Tahap 1*: Harga produk + handling fee (dibayar saat checkout).
  2. *Invoice Tahap 2*: Ongkir bagasi jastip internasional & kurir lokal (dibayar saat barang ditimbang & siap dikirim).
- Penanganan Barang Habis di Jepang (*Out of Stock Resolution* - Skema B Auto-Offset).
- Manifest Bagasian & Koper (*Shipment Bagasian*) dengan export dokumen PDF.
- Panel Admin untuk kelola trip, pesanan, kargo, status pesanan, dan pengembalian dana (*refund*).

---

## 📖 Dokumentasi Lengkap
Dokumentasi teknis yang mudah dibaca dan dipahami telah disiapkan:
- 📘 **[Panduan Lengkap Backend Galaksian](docs/BACKEND_GUIDE.md)**: Penjelasan arsitektur, rumus hitungan harga, alur webhook idempotent, siklus status pesanan, dan katalog endpoint API.
- 📊 **[Diagram Arsitektur & Alur Mermaid](docs/BACKEND_DIAGRAMS.md)**: Visualisasi flowchart arsitektur, alur jastip 2 tahap, dan ERD database.
- 📋 **[Panduan Developer & AI Agent (AGENTS.md)](AGENTS.md)**: Prinsip arsitektur, aturan testing, dan standar kode.
- 📑 **[Spesifikasi Teknis Lengkap (BACKEND_CONTEXT.md)](docs/BACKEND_CONTEXT.md)**: Konteks spesifikasi menyeluruh.

---

## ⚡ Panduan Menjalankan Aplikasi

```bash
# 1. Install dependensi
composer install

# 2. Siapkan database PostgreSQL dan user
sudo -u postgres psql -c "CREATE ROLE galaksian LOGIN PASSWORD 'galaksian_dev_password';"
sudo -u postgres psql -c "CREATE DATABASE galaksian OWNER galaksian;"

# 3. Salin environment dan generate key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi koneksi di .env (pgsql, host/port/db/user/password)

# 5. Jalankan migrasi dan seed database
php artisan migrate --seed

# 6. Buat symbolic link storage
php artisan storage:link

# 7. Jalankan server lokal
php artisan serve
```

### Akun Demo Uji Coba:
- **Pengguna (User)**: `081234567890` (Bisa menggunakan tombol *Login Cepat* di web).
- **Administrator (Admin)**: `admin@galaksian.com` / `password`.

---

## 🧪 Testing & Code Style

```bash
# Menjalankan seluruh automated test suite
php artisan test

# Memeriksa dan memformat code style
./vendor/bin/pint
```
