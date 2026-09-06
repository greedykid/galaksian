# AGENTS.md

Dokumen ini menjadi panduan untuk AI agent dan developer yang bekerja di repository Galaksian, khususnya backend Laravel.

---

## 1. Konteks Proyek

Galaksian adalah backend mobile web e-commerce/jastip Indonesia–Jepang dengan fitur utama:

- Auth OTP nomor handphone
- Home, search, banner, brand, kategori, flash sale, rekomendasi
- Keranjang
- Checkout 2 tahap:
  - invoice produk
  - invoice pengiriman
- Admin panel untuk produk, order, transaksi, pembayaran, shipment, refund
- PDF invoice dan dokumen Bagasian
- Integrasi WhatsApp

---

## 2. Stack Utama

| Teknologi | Keterangan |
|---|---|
| Laravel | Versi target: Laravel 13 |
| PHP | Versi terbaru yang didukung Laravel 13 |
| MySQL/PostgreSQL | Database utama |
| Redis | Cache, queue, session bila digunakan |
| Laravel Sanctum | Auth API/token |
| Laravel Queue | Proses async |
| Pest/PHPUnit | Testing |
| Laravel Pint | Code style |
| PHPStan/Larastan | Static analysis |
| Dompdf/Spatie PDF | Generate PDF |
| Laravel Excel | Import produk |
| Storage | Local/S3 |

---

## 3. Prinsip Utama

Semua agent wajib mengikuti prinsip berikut:

1. **Business logic tidak boleh berada di controller**
   - Controller hanya validasi request, memanggil service, dan return response.

2. **Semua perhitungan harga dihitung backend**
   - Jangan percaya total dari frontend.
   - Subtotal, diskon, voucher, handling fee, ongkir dihitung ulang di server.

3. **Gunakan service layer**
   Contoh:
   - `CheckoutService`
   - `PricingCalculator`
   - `PaymentGatewayService`
   - `InvoiceService`
   - `OrderStatusService`
   - `ShipmentService`
   - `RefundService`
   - `WhatsappService`

4. **Gunakan Form Request**
   - Validasi input harus menggunakan Form Request.

5. **Gunakan API Resource**
   - Jangan return model mentah ke frontend.

6. **Gunakan PHP Enum**
   - Untuk status order, invoice, payment, shipment, availability.

7. **Gunakan DB transaction untuk proses kritis**
   - Checkout
   - Payment capture
   - Refund
   - Update stock
   - Assign shipment

8. **Webhook harus idempotent**
   - Jangan proses event pembayaran dua kali.

9. **Semua perubahan status order harus dicatat**
   - Gunakan `order_status_histories`.

10. **Jangan hard delete data transaksi**
   - Order, invoice, payment, refund harus audit-friendly.

---

## 4. Struktur Direktori yang Disarankan

```text
app/
  Enums/
  Models/
  Http/
    Controllers/
      Api/V1/User/
      Api/V1/Admin/
      Webhooks/
    Requests/
      User/
      Admin/
    Resources/
  Services/
  Support/
  Policies/
  Jobs/
  Events/
  Listeners/
  Notifications/
  Observers/
database/
  migrations/
  factories/
  seeders/
docs/
  PRD.md
  BACKEND_CONTEXT.md
routes/
  api.php
  admin.php
  webhooks.php
storage/
  app/
    private/
      invoices/
      bagasian/
      addresses/
tests/
  Feature/
  Unit/
```

---

## 5. Aturan Endpoint

Gunakan:

- versioning `/api/v1`
- plural untuk collection
- kebab-case
- resource-based naming

Contoh:

```text
GET    /api/v1/products
GET    /api/v1/products/{slug}
POST   /api/v1/cart/items
PATCH  /api/v1/cart/items/{id}
POST   /api/v1/checkout
GET    /api/v1/orders
GET    /api/v1/orders/{id}
POST   /api/v1/admin/orders/{id}/assign-shipment
POST   /api/v1/admin/shipments/{id}/send-bagasian
```

---

## 6. Aturan Response API

Format sukses:

```json
{
  "success": true,
  "message": "OK",
  "data": {}
}
```

Format error validasi:

```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "phone": ["The phone field is required."]
  }
}
```

Format error bisnis:

```json
{
  "success": false,
  "message": "Tidak ada trip aktif untuk checkout"
}
```

---

## 7. Aturan Database

1. Gunakan `id` bigint unsigned auto increment.
2. Gunakan `timestamps()`.
3. Gunakan `softDeletes()` untuk entitas penting seperti product, user, order bila relevan.
4. Nominal uang disimpan sebagai `bigint` atau `integer`.
5. Jangan gunakan float untuk uang.
6. Tambahkan index untuk kolom yang sering difilter:
   - `user_id`
   - `order_id`
   - `product_id`
   - `status`
   - `invoice_id`
   - `shipment_id`
   - `phone`
   - `slug`
   - `sku`
7. Gunakan foreign key untuk relasi kuat.
8. Snapshot data penting ke order:
   - harga produk
   - nama produk
   - brand
   - alamat

---

## 8. Aturan Validasi

Gunakan Form Request untuk:

- auth OTP
- address
- cart item
- checkout
- product
- invoice
- refund
- shipment

Contoh aturan penting:

### Checkout

```text
address_id: required, exists, owned by user
payment_method: required, in virtual_account,paypal,qris
voucher_code: nullable, exists active
notes: nullable, string, max:1000
```

### Address

```text
recipient_name: required
phone: required
address: required
photo: nullable, image, max:2048
api_address: nullable, string
delivery_note: nullable, in dropdown
```

### Product

```text
name: required
slug: unique
sku: nullable, unique
price: required, integer, min:0
discount_price: nullable, integer, min:0
stock: required, integer, min:0
availability_type: required, in ready_stock,open_po
```

---

## 9. Domain Rules yang Wajib Dijaga

Agent harus memastikan rules berikut selalu dijaga:

1. Guest boleh search dan browse.
2. Checkout wajib login.
3. Checkout gagal jika tidak ada trip aktif.
4. Produk ready stock harus cek stock.
5. Produk open PO boleh order sesuai aturan availability.
6. Satu shipment bisa memiliki banyak order.
7. Invoice produk dibuat saat checkout.
8. Invoice pengiriman dibuat ketika order siap diantar atau admin trigger.
9. Invoice tambahan dapat dibuat admin.
10. Invoice paid tidak boleh diubah nominal.
11. Perubahan status order harus masuk history.
12. Total order dihitung backend.
13. Diskon pengguna baru hanya sekali.
14. Voucher harus divalidasi masa aktif, kuota, minimal order.
15. Webhook payment harus idempotent.
16. Refund tidak boleh menghapus order.

---

## 10. Perintah Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

php artisan serve
php artisan queue:work
php artisan schedule:work
```

Testing:

```bash
php artisan test
```

Code style:

```bash
./vendor/bin/pint
```

Static analysis:

```bash
./vendor/bin/phpstan analyse
```

---

## 11. Pola Penulisan Controller

Controller harus tipis.

Contoh benar:

```php
public function store(CheckoutRequest $request, CheckoutService $service)
{
    $order = $service->checkout($request->user(), $request->validated());

    return CheckoutResource::make($order);
}
```

Jangan:

```php
// Jangan menghitung total, membuat invoice, dan mengubah stock langsung di controller.
```

---

## 12. Pola Service Layer

Service yang wajib ada:

### CheckoutService

Tanggung jawab:

1. Validasi trip aktif.
2. Ambil cart user.
3. Validasi stock.
4. Hitung pricing.
5. Buat order.
6. Buat order items snapshot.
7. Buat invoice produk.
8. Buat payment intent.
9. Catat history.
10. Kirim notification/job.

### PricingCalculator

Tanggung jawab:

1. Hitung subtotal.
2. Hitung promo produk.
3. Hitung voucher.
4. Hitung diskon user baru.
5. Hitung handling fee.
6. Hitung product total.
7. Hitung shipping total bila sudah ada ongkir.

### OrderStatusService

Tanggung jawab:

1. Validasi transisi status legal.
2. Simpan history.
3. Trigger event/notification.
4. Cegah perubahan status ilegal.

### InvoiceService

Tanggung jawab:

1. Buat invoice product.
2. Buat invoice shipping.
3. Buat invoice additional.
4. Cegah edit invoice paid.

### PaymentGatewayService

Tanggung jawab:

1. Buat charge/payment intent.
2. Handle VA, QRIS, PayPal.
3. Verifikasi webhook.
4. Update invoice/payment.
5. Update order status.

### ShipmentService

Tanggung jawab:

1. Assign order ke shipment.
2. Update estimasi packing.
3. Generate PDF Bagasian.
4. Buat WhatsApp link/kirim dokumen.

### RefundService

Tanggung jawab:

1. Buat refund.
2. Hitung nominal refund.
3. Update status order/item.
4. Catat history.

### WhatsappService

Tanggung jawab:

1. Buat link `wa.me`.
2. Kirim dokumen bila memakai WA Business API.
3. Simpan log pengiriman.

---

## 13. Aturan Testing

Setiap fitur penting wajib punya test.

Minimal:

1. Feature test endpoint.
2. Unit test pricing.
3. Test validasi.
4. Test authorization.
5. Test state transition.
6. Test webhook idempotency.

Test penting:

- Guest tidak bisa checkout.
- Checkout gagal tanpa trip aktif.
- Checkout berhasil membuat invoice produk.
- Pembayaran sukses mengubah status order.
- Invoice paid tidak bisa diubah.
- Admin dapat mengubah status order.
- Perubahan status tercatat di history.
- Refund dapat dibuat untuk order paid.
- Voucher expired ditolak.
- Diskon user baru hanya sekali.
- Produk ready stock habis tidak bisa checkout.
- Produk open PO bisa checkout sesuai aturan.

---

## 14. Keamanan

Agent wajib memperhatikan:

1. OTP tidak boleh disimpan plain text.
2. OTP memiliki expiry dan max attempt.
3. Rate limit endpoint auth.
4. Gunakan Sanctum untuk token.
5. Middleware admin terpisah.
6. Policy untuk order/address/invoice milik user.
7. Webhook diverifikasi signature-nya.
8. File upload divalidasi.
9. Jangan log OTP/token/credential payment.
10. Jangan simpan data kartu.
11. Jangan tampilkan invoice/order user lain.
12. Jika menyimpan KTP, gunakan encryption.

---

## 15. Definition of Done

Fitur dianggap selesai jika:

1. Endpoint berfungsi sesuai requirement.
2. Validasi lengkap.
3. Authorization dicek.
4. Business rule dijaga.
5. Test lulus.
6. Tidak ada query N+1 yang jelas.
7. Response konsisten.
8. Dokumentasi backend diperbarui.
9. Code style bersih.
10. Tidak ada secret hardcoded.

---

## 16. Hal yang Dilarang

1. Dilarang menghitung total order di frontend lalu dipercaya backend.
2. Dilarang mengubah invoice paid secara langsung.
3. Dilarang menghapus order/invoice/payment secara hard delete.
4. Dilarang menaruh logic pembayaran di controller.
5. Dilarang menggunakan float untuk uang.
6. Dilarang membuat endpoint admin tanpa middleware role.
7. Dilarang bypass validasi stock.
8. Dilarang memproses webhook dua kali.
9. Dilarang mengubah status order tanpa history.
10. Dilarang menyimpan OTP plain text.

---

## 17. Kapan Agent Harus Bertanya

Agent harus bertanya atau menandai clarification jika:

1. Aturan diskon belum jelas.
2. Payment gateway belum ditentukan.
3. Aturan refund belum final.
4. Field `api alamat` belum jelas.
5. Aturan trip aktif belum jelas.
6. Ada perubahan flow pembayaran.
7. Ada kebutuhan multi-currency settlement.
8. Ada kebutuhan notifikasi channel baru.
9. Ada perubahan struktur invoice.
10. Ada requirement pajak.

---

## 18. Panduan Menambah Fitur Baru

Untuk fitur baru:

1. Baca PRD.
2. Tentukan apakah butuh migration.
3. Buat/ubah enum bila perlu.
4. Buat Form Request.
5. Tambah controller endpoint.
6. Implementasi service.
7. Tambah policy bila perlu.
8. Tambah resource.
9. Tambah job/event/notification bila async.
10. Tambah test.
11. Update dokumentasi backend.

---

## 19. Panduan Mengubah Status Order

Jika mengubah status order:

1. Gunakan `OrderStatusService`.
2. Validasi transisi legal.
3. Simpan history.
4. Trigger event bila diperlukan.
5. Kirim notifikasi jika dibutuhkan.
6. Jangan ubah status langsung dari controller.

---

## 20. Panduan Pembayaran

1. Payment dibuat dari invoice.
2. Payment method disimpan di payment/invoice.
3. Webhook disimpan di `webhook_events`.
4. Webhook harus idempotent.
5. Invoice paid menandai payment paid.
6. Jika invoice produk paid, order menjadi `paid_product`.
7. Jika invoice ongkir paid, order menjadi `shipping_paid`.

---

## 21. Panduan PDF dan WhatsApp

1. PDF invoice dan Bagasian disimpan di storage private.
2. Nama file unik.
3. Download invoice harus cek ownership.
4. WhatsApp link dapat dibuat dari nomor tujuan + teks encoded.
5. Jika memakai WA Business API, log response.
6. Kirim file besar sebaiknya menggunakan queue.