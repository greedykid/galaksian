# Backend Context Galaksian

Dokumen ini adalah konteks teknis backend untuk developer dan AI agent. Tujuannya adalah memudahkan maintenance, penambahan fitur, dan pemahaman alur bisnis.

---

## 1. Gambaran Sistem

Backend Galaksian bertanggung jawab untuk:

1. Auth user berbasis nomor handphone.
2. Menyediakan data home dan katalog:
   - banner
   - search
   - brand
   - kategori
   - flash sale
   - special for you
   - beli lagi
   - produk terlaris
   - promo produk
3. Mengelola keranjang.
4. Checkout invoice produk.
5. Integrasi pembayaran:
   - Virtual Account
   - PayPal
   - QRIS
6. Mengelola transaksi user.
7. Admin panel:
   - produk
   - order
   - transaksi
   - pembayaran
   - shipment
   - refund
8. Generate PDF:
   - invoice
   - dokumen Bagasian
9. Membuat link/kirim WhatsApp.
10. Mencatat history dan audit.

---

## 2. Arsitektur Backend

Pola arsitektur:

```text
Frontend Mobile Web
        |
        v
API Controller
        |
        v
Form Request Validation
        |
        v
Service Layer
        |
        v
Eloquent Model / Database
        |
        v
Queue + Storage + Payment + WhatsApp
```

Service eksternal:

- Payment gateway
- WhatsApp / WA Business API
- Email opsional
- Storage local/S3
- Bagasian manual/API

---

## 3. Modul Backend

### 3.1 Auth Module

Tanggung jawab:

- OTP request
- OTP verify
- login/register
- logout
- profile me

Entitas:

- `users`
- `otp_codes`

Aturan:

- OTP hashed.
- OTP punya expiry.
- OTP punya max attempt.
- Nomor handphone sebaiknya E.164, contoh `6281234567890`.
- Guest boleh akses catalog.
- Checkout wajib auth.

---

### 3.2 Catalog Module

Tanggung jawab:

- home payload
- product list
- product detail
- search
- brand list
- category list
- brand products
- category products
- flash sale
- best seller
- promo product
- special for you
- beli lagi

Sumber data:

- `products`
- `brands`
- `categories`
- `banners`
- `order_items` untuk beli lagi/terlaris

Catatan:

- Guest boleh akses.
- Search harus mencakup nama produk, brand, kategori.
- Toggle Indonesia/Jepang dapat difilter berdasarkan `origin_country`.
- Flash sale perlu countdown berdasarkan `flash_sale_start_at` dan `flash_sale_end_at`.

---

### 3.3 Cart Module

Tanggung jawab:

- add item
- update qty
- remove item
- apply voucher
- cart summary

Entitas:

- `carts`
- `cart_items`

Aturan:

- Harga dihitung backend.
- Total ongkir tidak ditampilkan pada cart/checkout pertama.
- Cart dapat milik user authenticated.
- Jika guest cart didukung, gunakan `cart_token` dan merge saat login.

---

### 3.4 Checkout Module

Tanggung jawab:

- validasi user
- validasi alamat
- validasi trip aktif
- validasi stock
- hitung pricing
- buat order
- buat order items snapshot
- buat invoice produk
- buat payment intent

Service:

- `CheckoutService`
- `PricingCalculator`
- `TripEligibilityService`
- `InvoiceService`
- `PaymentGatewayService`

Aturan:

- Checkout harus DB transaction.
- Snapshot harga dan alamat wajib disimpan.
- Jangan percaya harga dari frontend.
- Checkout gagal jika tidak ada trip aktif.

---

### 3.5 Payment Module

Tanggung jawab:

- create payment charge
- handle VA, QRIS, PayPal
- handle webhook
- update invoice/payment
- update order status

Entitas:

- `invoices`
- `payments`
- `webhook_events`

Aturan:

- Webhook idempotent.
- Simpan raw payload.
- Verifikasi signature.
- Jangan proses event dua kali.

---

### 3.6 Order / Transaction Module

Tanggung jawab:

- list order user
- detail order
- status tracking
- invoice list
- download invoice
- WhatsApp contact
- review
- beli lagi

Entitas:

- `orders`
- `order_items`
- `order_status_histories`
- `invoices`
- `reviews`

Aturan:

- User hanya boleh melihat order miliknya.
- Transaksi pending mencakup invoice produk dan invoice ongkir.
- Order ulang tidak muncul sebelum status siap diantar.
- Setelah siap diantar, user perlu membayar invoice ongkir.

---

### 3.7 Address Module

Tanggung jawab:

- CRUD alamat
- default address
- upload foto lokasi
- catatan pengiriman dropdown

Field utama:

- `user_id`
- `recipient_name`
- `phone`
- `address`
- `photo_path`
- `api_address`
- `delivery_note`
- `is_default`

Catatan:

- `api_address` masih perlu klarifikasi. Simpan nullable string sampai ada definisi final.
- `delivery_note` sebaiknya dropdown dari enum/config.
- Foto alamat divalidasi sebagai image.

---

### 3.8 Shipment / Bagasian Module

Tanggung jawab:

- shipment CRUD/assign
- packing estimate
- send PDF to Bagasian
- WhatsApp share
- ongkir per order

Entitas:

- `shipments`
- `orders`

Aturan:

- Satu shipment dapat memiliki banyak order.
- Order memiliki `shipment_id`.
- Admin mengisi:
  - `shipping_jastip_amount`
  - `shipping_local_amount`
- Estimasi packing ditentukan admin setelah dikirim ke Bagasian.
- Setelah siap diantar, invoice ongkir dapat dibuat.

---

### 3.9 Admin Module

Tanggung jawab:

- admin auth
- dashboard
- product CRUD/import
- category/brand CRUD
- banner CRUD
- trip CRUD
- order management
- invoice management
- refund management
- user/profile management

Aturan:

- Admin memakai middleware role terpisah.
- Aksi sensitif dicatat di activity/audit log.

---

## 4. Struktur Database yang Disarankan

### 4.1 users

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| name | string | Nama |
| phone | string unique | Nomor HP |
| email | string nullable | Opsional |
| password | string nullable | Jika pakai password |
| identity_number | string nullable | KTP opsional, sebaiknya encrypted |
| role | string | user/admin/cs |
| language | string | id/en |
| is_new_user | boolean | Promo user baru |
| new_user_promo_used_at | timestamp nullable | Penanda promo dipakai |
| last_login_at | timestamp nullable | Login terakhir |
| timestamps | | |
| deleted_at | | soft delete |

---

### 4.2 otp_codes

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| phone | string | Nomor tujuan |
| code_hash | string | Hash OTP |
| purpose | string | login/register |
| expires_at | timestamp | Kadaluarsa |
| attempts | integer | Jumlah coba |
| consumed_at | timestamp nullable | Sudah dipakai |
| ip_address | string nullable | Audit |

---

### 4.3 addresses

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| user_id | bigint FK | Milik user |
| label | string nullable | Rumah/Kantor |
| recipient_name | string | Nama penerima |
| phone | string | Nomor penerima |
| address | text | Alamat utama |
| photo_path | string nullable | Foto lokasi |
| api_address | string nullable | Perlu klarifikasi |
| delivery_note | string nullable | Dropdown |
| is_default | boolean | Alamat utama |
| country | string nullable | Negara |
| province | string nullable | Provinsi |
| city | string nullable | Kota |
| district | string nullable | Kecamatan |
| postal_code | string nullable | Kode pos |
| timestamps | | |

---

### 4.4 brands

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| name | string | Nama brand |
| slug | string unique | Slug |
| logo_path | string nullable | Logo |
| is_active | boolean | Aktif |
| timestamps | | |

---

### 4.5 categories

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| name | string | Nama kategori |
| slug | string unique | Slug |
| image_path | string nullable | Gambar |
| is_active | boolean | Aktif |
| timestamps | | |

---

### 4.6 banners

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| title | string nullable | Judul |
| image_path | string | Gambar |
| link_url | string nullable | CTA/link |
| cta_text | string nullable | Text CTA |
| order | integer | Urutan |
| is_active | boolean | Aktif |
| locale | string nullable | id/en |
| country_filter | string nullable | ID/JP/all |
| timestamps | | |

---

### 4.7 trips

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| code | string unique | Kode trip |
| origin_country | string | ID/JP |
| destination_country | string | ID/JP |
| departure_at | timestamp nullable | Berangkat |
| arrival_at | timestamp nullable | Tiba |
| cutoff_at | timestamp nullable | Batas order |
| status | string | draft/active/closed |
| notes | text nullable | Catatan |
| timestamps | | |

---

### 4.8 products

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| sku | string unique nullable | SKU |
| slug | string unique | Slug |
| name | string | Nama produk |
| description | text nullable | Deskripsi |
| brand_id | bigint FK | Brand |
| category_id | bigint FK nullable | Kategori utama |
| origin_country | string | ID/JP |
| currency | string | IDR/JPY |
| price | bigint | Harga normal |
| discount_price | bigint nullable | Harga promo |
| stock | integer | Stock ready |
| low_stock_threshold | integer nullable | Batas stock rendah |
| availability_type | string | ready_stock/open_po |
| is_active | boolean | Aktif tampil |
| is_flash_sale | boolean | Flash sale |
| flash_sale_start_at | timestamp nullable | Mulai flash sale |
| flash_sale_end_at | timestamp nullable | Akhir flash sale |
| weight_gram | integer nullable | Berat |
| length_cm | integer nullable | Panjang |
| width_cm | integer nullable | Lebar |
| height_cm | integer nullable | Tinggi |
| timestamps | | |
| deleted_at | | soft delete |

---

### 4.9 product_images

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| product_id | bigint FK | Produk |
| path | string | Gambar |
| order | integer | Urutan |
| is_primary | boolean | Gambar utama |

---

### 4.10 carts

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| user_id | bigint FK nullable | User |
| cart_token | string nullable | Guest cart |
| status | string | active/merged/abandoned |
| currency | string | IDR |
| timestamps | | |

---

### 4.11 cart_items

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| cart_id | bigint FK | Cart |
| product_id | bigint FK | Produk |
| qty | integer | Jumlah |
| timestamps | | |

---

### 4.12 vouchers

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| code | string unique | Kode |
| type | string | fixed/percent |
| value | bigint | Nilai diskon |
| max_discount | bigint nullable | Max diskon untuk percent |
| min_order_amount | bigint nullable | Minimal order |
| usage_limit | integer nullable | Kuota total |
| usage_per_user | integer nullable | Kuota per user |
| applicable_scope | string | product/shipping/all |
| starts_at | timestamp | Mulai |
| ends_at | timestamp | Akhir |
| is_active | boolean | Aktif |
| timestamps | | |

---

### 4.13 orders

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| order_number | string unique | Nomor order |
| user_id | bigint FK | User |
| trip_id | bigint FK nullable | Trip |
| shipment_id | bigint FK nullable | Shipment |
| status | string | Status order |
| currency | string | IDR |
| exchange_rate | bigint nullable | Kurs bila perlu |
| address_snapshot | json | Snapshot alamat |
| product_subtotal | bigint | Subtotal produk |
| product_discount_amount | bigint | Diskon produk |
| new_user_discount_amount | bigint | Diskon user baru |
| voucher_id | bigint FK nullable | Voucher |
| voucher_amount | bigint | Nominal voucher |
| handling_fee_amount | bigint | Biaya penanganan |
| product_total | bigint | Total tagihan produk |
| shipping_jastip_amount | bigint nullable | Ongkir jastip |
| shipping_local_amount | bigint nullable | Ongkir lokal |
| shipping_total | bigint nullable | Total ongkir |
| grand_total | bigint nullable | Total produk + ongkir |
| notes | text nullable | Catatan |
| product_paid_at | timestamp nullable | Invoice produk paid |
| shipping_paid_at | timestamp nullable | Invoice ongkir paid |
| completed_at | timestamp nullable | Selesai |
| cancelled_at | timestamp nullable | Batal |
| cancel_reason | string nullable | Alasan batal |
| timestamps | | |
| deleted_at | | soft delete |

---

### 4.14 order_items

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| order_id | bigint FK | Order |
| product_id | bigint FK | Produk |
| product_name_snapshot | string | Nama produk saat order |
| brand_name_snapshot | string nullable | Brand saat order |
| sku_snapshot | string nullable | SKU saat order |
| qty | integer | Qty |
| unit_price | bigint | Harga satuan final |
| original_price | bigint | Harga asli |
| discount_amount | bigint | Diskon item |
| subtotal | bigint | qty x unit price |
| availability_type | string | ready_stock/open_po |
| refund_status | string nullable | none/requested/refunded |
| refund_amount | bigint nullable | Nominal refund item |
| timestamps | | |

---

### 4.15 shipments

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| shipment_number | string unique | Nomor shipment |
| trip_id | bigint FK nullable | Trip |
| status | string | draft/packing/sent/delivering/completed |
| origin_country | string | Asal |
| destination_country | string | Tujuan |
| bagasian_reference | string nullable | Ref Bagasian |
| packing_estimate_weight | string nullable | Estimasi berat |
| packing_estimate_volume | string nullable | Estimasi volume |
| packing_estimate_cost | bigint nullable | Estimasi biaya |
| sent_to_bagasian_at | timestamp nullable | Waktu kirim PDF |
| bagasian_pdf_path | string nullable | File PDF |
| wa_message | text nullable | Pesan WA |
| notes | text nullable | Catatan |
| timestamps | | |

---

### 4.16 invoices

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| invoice_number | string unique | Nomor invoice |
| order_id | bigint FK | Order |
| type | string | product/shipping/additional |
| status | string | pending/paid/expired/failed/refunded |
| amount | bigint | Nominal |
| description | text nullable | Deskripsi |
| payment_method | string nullable | va/qris/paypal |
| payment_gateway | string nullable | Gateway |
| gateway_reference | string nullable | Ref gateway |
| paid_at | timestamp nullable | Waktu bayar |
| expired_at | timestamp nullable | Kadaluarsa |
| created_by | bigint nullable | Admin/user |
| payload | json nullable | Payload tambahan |
| timestamps | | |

---

### 4.17 payments

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| invoice_id | bigint FK | Invoice |
| method | string | va/qris/paypal |
| status | string | pending/paid/failed/expired/refund |
| amount | bigint | Nominal |
| gateway_reference | string nullable | Ref |
| raw_payload | json nullable | Payload gateway |
| paid_at | timestamp nullable | Paid |
| failed_at | timestamp nullable | Failed |
| refunded_at | timestamp nullable | Refund |
| timestamps | | |

---

### 4.18 refunds

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| order_id | bigint FK | Order |
| invoice_id | bigint FK nullable | Invoice terkait |
| reason | text | Alasan |
| amount | bigint | Nominal |
| status | string | pending/approved/rejected/completed |
| approved_by | bigint nullable | Admin |
| refund_method | string nullable | Metode refund |
| evidence_path | string nullable | Bukti |
| timestamps | | |

---

### 4.19 order_status_histories

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| order_id | bigint FK | Order |
| from_status | string nullable | Status lama |
| to_status | string | Status baru |
| note | text nullable | Catatan |
| actor_type | string | user/admin/system |
| actor_id | bigint nullable | ID actor |
| timestamps | | |

---

### 4.20 reviews

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| user_id | bigint FK | User |
| product_id | bigint FK | Produk |
| order_id | bigint FK | Order |
| rating | integer | 1-5 |
| comment | text nullable | Komentar |
| images | json nullable | Foto review |
| status | string | pending/published/rejected |
| timestamps | | |

---

### 4.21 notifications

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| user_id | bigint FK | User |
| type | string | order/payment/refund/promo |
| title | string | Judul |
| body | text | Isi |
| data | json nullable | Payload |
| read_at | timestamp nullable | Sudah dibaca |
| timestamps | | |

---

### 4.22 webhook_events

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| source | string | Payment gateway |
| event_id | string unique nullable | ID event |
| event_type | string | Tipe event |
| payload | json | Payload |
| signature | string nullable | Signature |
| status | string | received/processed/failed |
| processed_at | timestamp nullable | Waktu proses |
| timestamps | | |

---

### 4.23 settings

| Kolom | Tipe | Keterangan |
|---|---|---|
| key | string PK | Kunci |
| value | text | Nilai |

Contoh key:

```text
admin_whatsapp_number
cs_whatsapp_number
handling_fee_default
new_user_discount_enabled
new_user_discount_amount
new_user_discount_percent
invoice_expiry_minutes
shipping_estimate_text
```

---

## 5. Enum yang Disarankan

### OrderStatus

```text
draft
pending_payment_product
paid_product
processing
packing
ready_for_delivery
pending_payment_shipping
shipping_paid
delivering
completed
cancelled
refund_requested
refunded
```

### InvoiceType

```text
product
shipping
additional
```

### InvoiceStatus

```text
pending
paid
expired
failed
refunded
```

### PaymentMethod

```text
virtual_account
qris
paypal
manual
```

### PaymentStatus

```text
pending
paid
failed
expired
refund
```

### ProductAvailability

```text
ready_stock
open_po
```

### ShipmentStatus

```text
draft
assigned
packing
sent_to_bagasian
ready_for_delivery
delivering
completed
cancelled
```

### AddressDeliveryNote

Contoh dropdown:

```text
leave_at_front_door
contact_before_delivery
hand_to_receiver
security_desk
other
```

---

## 6. Flow Checkout Detail

### Input

- user authenticated
- address_id
- payment_method
- voucher_code opsional
- notes opsional

### Validasi

1. User aktif.
2. Address milik user.
3. Cart tidak kosong.
4. Produk aktif.
5. Stock cukup untuk ready stock.
6. Trip aktif tersedia.
7. Voucher valid jika dipakai.
8. Payment method valid.

### Proses

1. Mulai DB transaction.
2. Lock product rows untuk stock check.
3. Hitung pricing:
   - product subtotal
   - promo
   - voucher
   - new user discount
   - handling fee
   - product total
4. Buat order.
5. Buat order_items snapshot.
6. Tandai voucher used bila ada.
7. Tandai new user promo used bila dipakai.
8. Buat invoice type product.
9. Buat payment record/payment intent.
10. Simpan order history.
11. Commit transaction.
12. Dispatch job notification/payment instruction.

### Output

- order
- invoice product
- payment instruction/link/QR

---

## 7. Flow Pembayaran

### Invoice Produk

1. User bayar dari checkout/transaksi.
2. Gateway mengirim webhook.
3. Backend verifikasi signature.
4. Cek `webhook_events.event_id` untuk idempotency.
5. Update payment status.
6. Update invoice status.
7. Jika paid:
   - order menjadi `paid_product`
   - kurangi stock jika kebijakan decrement on paid
   - kirim notifikasi

### Invoice Pengiriman

1. Admin mengisi ongkir.
2. Order siap diantar.
3. Sistem/admin membuat invoice shipping.
4. User bayar.
5. Webhook masuk.
6. Jika paid:
   - order menjadi `shipping_paid`
   - kirim notifikasi

### Invoice Tambahan

1. Admin membuat invoice additional.
2. User bayar terpisah.
3. Invoice marked paid.
4. Order tetap mengikuti status utama.

---

## 8. Flow Admin Shipment

1. Admin membuat shipment.
2. Admin assign satu atau banyak order ke shipment.
3. Admin update status shipment.
4. Admin generate PDF bila perlu.
5. Admin mengirim PDF ke Bagasian/WhatsApp.
6. Admin mengisi estimasi packing:
   - weight
   - volume
   - cost
7. Admin mengisi ongkir per order:
   - shipping_jastip_amount
   - shipping_local_amount
8. Admin set order `ready_for_delivery`.
9. Sistem membuat invoice shipping.
10. User membayar ongkir.
11. Admin proses delivering.
12. Admin set completed.

---

## 9. Flow PDF dan WhatsApp

### Invoice PDF

1. User/admin request download invoice.
2. Backend cek akses.
3. PDF digenerate dari invoice + order.
4. Simpan ke storage atau stream langsung.
5. Return download response/signed URL.

### Bagasian PDF

1. Admin memilih shipment/order.
2. Backend mengumpulkan data:
   - shipment info
   - order list
   - alamat
   - item list
   - estimasi packing
3. PDF digenerate.
4. Simpan path di shipment.
5. Jika WA link:
   - buat pesan otomatis
   - encode URL
   - return `https://wa.me/<number>?text=...`
6. Jika WA API:
   - dispatch job kirim dokumen
   - simpan log

---

## 10. Pricing Calculator

Service: `PricingCalculator`.

### Input

- cart items
- product prices
- voucher
- user flag new user
- handling fee config
- admin ongkir untuk shipping

### Output

- product_subtotal
- product_discount_amount
- new_user_discount_amount
- voucher_amount
- handling_fee_amount
- product_total
- shipping_jastip_amount
- shipping_local_amount
- shipping_total
- grand_total

### Aturan

1. Semua nilai integer.
2. Jangan biarkan total negatif.
3. Simpan snapshot harga di order_items.
4. Diskon produk dapat berasal dari `discount_price`.
5. Voucher hanya berlaku jika valid.
6. New user discount hanya sekali.
7. Handling fee dapat berasal dari settings atau admin override.

---

## 11. Kebijakan Stock

### Ready Stock

- Stock harus dicek saat add to cart/update qty/checkout.
- Rekomendasi:
  - stock check saat checkout
  - decrement saat payment paid
  - atau reservation sementara dengan expiry

### Open PO

- Dapat dipesan meskipun stock fisik tidak tersedia.
- Gunakan `availability_type = open_po`.
- Tetap butuh trip aktif.
- Aturan kuota/cutoff PO perlu dikonfirmasi.

---

## 12. Kebijakan Voucher

Validasi voucher:

1. `is_active = true`
2. `starts_at <= now`
3. `ends_at >= now`
4. `usage_limit` belum habis
5. `usage_per_user` belum habis
6. `min_order_amount` terpenuhi
7. scope sesuai:
   - product
   - shipping
   - all

Rekomendasi MVP:

- Gunakan scope product dulu.
- Voucher shipping dapat ditambah nanti.

---

## 13. Kebijakan Diskon User Baru

Aturan awal:

1. Berlaku jika user baru.
2. Berlaku pada order pertama yang created/paid sesuai keputusan bisnis.
3. Setelah dipakai, set `new_user_promo_used_at`.
4. Nominal bisa fixed atau percent dari settings.
5. Tidak berlaku untuk order berikutnya.

---

## 14. Middleware yang Dibutuhkan

### User API

```text
auth:sanctum
throttle
localize
```

### Admin API

```text
auth:sanctum
admin role
throttle
activity log
```

### Webhook

```text
verify signature
raw body
throttle by gateway/event
```

---

## 15. Event dan Job

### Events

```text
OrderCreated
OrderStatusChanged
InvoiceCreated
InvoicePaid
PaymentReceived
ShippingInvoiceCreated
RefundRequested
RefundCompleted
```

### Jobs

```text
SendOrderNotification
GenerateInvoicePdf
GenerateBagasianPdf
SendWhatsappDocument
ExpirePendingPayments
CleanupGuestCarts
RecalculateDashboardStats
```

### Scheduler

```text
expire unpaid invoices
close expired flash sales
update trip status
cleanup stuck webhook
daily summary
```

---

## 16. Environment Variables

```env
APP_NAME=Galaksian
APP_URL=
FRONTEND_URL=

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=galaksian
DB_USERNAME=galaksian
DB_PASSWORD=galaksian_dev_password

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

SANCTUM_STATEFUL_DOMAINS=

PAYMENT_GATEWAY=midtrans_xendit_custom
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
XENDIT_API_KEY=
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=

WHATSAPP_PROVIDER=wa_me_or_wa_business
WA_BUSINESS_TOKEN=
WA_BUSINESS_PHONE_ID=
ADMIN_WHATSAPP_NUMBER=
CS_WHATSAPP_NUMBER=

BAGASIAN_MODE=manual_or_api
BAGASIAN_API_URL=
BAGASIAN_API_KEY=

FILESYSTEM_DISK=local_or_s3
AWS_BUCKET=
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
```

---

## 17. Endpoint Detail Penting

### 17.1 Home

```text
GET /api/v1/home
```

Respons ideal berisi:

- banners
- flash_sales
- brands
- categories
- special_for_you
- beli_lagi
- best_sellers
- promo_products
- product_grid initial

Catatan:

- Guest boleh akses.
- `beli_lagi` hanya muncul jika user login dan punya riwayat.

---

### 17.2 Product List

```text
GET /api/v1/products
```

Query params:

```text
q
brand
category
country
availability
sort
page
per_page
```

Catatan:

- Search mencakup nama produk, brand, kategori.
- Pagination wajib.

---

### 17.3 Cart

```text
GET    /api/v1/cart
POST   /api/v1/cart/items
PATCH  /api/v1/cart/items/{id}
DELETE /api/v1/cart/items/{id}
POST   /api/v1/cart/voucher
```

Response cart sebaiknya berisi:

- items
- total_qty
- product_subtotal
- discount summary
- voucher applied
- product_total

---

### 17.4 Checkout

```text
POST /api/v1/checkout
```

Body contoh:

```json
{
  "address_id": 1,
  "payment_method": "qris",
  "voucher_code": "GALAKSIAN10",
  "notes": "Titip di resepsionis"
}
```

Response:

- order
- invoice product
- payment instruction

---

### 17.5 Order Detail

```text
GET /api/v1/orders/{id}
```

Harus berisi:

- order status
- items
- address snapshot
- invoices
- payments
- shipping summary
- actions:
  - pay
  - download invoice
  - contact WA

---

### 17.6 Admin Update Order Status

```text
PATCH /api/v1/admin/orders/{id}/status
```

Body contoh:

```json
{
  "status": "ready_for_delivery",
  "note": "Packing selesai, siap diantar"
}
```

Aturan:

- Validasi transisi status.
- Simpan history.
- Jika status ready_for_delivery dan ongkir sudah diisi, buat invoice shipping bila belum ada.

---

### 17.7 Admin Create Invoice

```text
POST /api/v1/admin/orders/{id}/invoices
```

Body contoh:

```json
{
  "type": "additional",
  "amount": 25000,
  "description": "Tambahan biaya packing"
}
```

Aturan:

- Invoice paid tidak boleh diedit.
- Jika butuh perubahan, buat invoice baru.

---

### 17.8 Admin Send Bagasian

```text
POST /api/v1/admin/shipments/{id}/send-bagasian
```

Response:

- pdf_url atau signed_url
- wa_link
- status sent

---

## 18. Service Classes yang Direkomendasikan

### CheckoutService

```php
checkout(User $user, array $payload): Order
```

Tanggung jawab:

- validasi cart
- validasi trip
- hitung harga
- buat order
- buat invoice product

---

### PricingCalculator

```php
calculateCart(Cart $cart, ?Voucher $voucher, User $user): PricingResult
calculateShipping(Order $order): ShippingPricingResult
```

---

### InvoiceService

```php
createProductInvoice(Order $order): Invoice
createShippingInvoice(Order $order): Invoice
createAdditionalInvoice(Order $order, array $data): Invoice
```

---

### PaymentGatewayService

```php
createCharge(Invoice $invoice, string $method): array
handleWebhook(array $payload, array $headers): void
```

---

### OrderStatusService

```php
transition(Order $order, OrderStatus $to, ?string $note, ?Model $actor): Order
```

---

### ShipmentService

```php
assignOrders(Shipment $shipment, array $orderIds): void
updatePackingEstimate(Shipment $shipment, array $data): void
sendToBagasian(Shipment $shipment): BagasianResult
```

---

### RefundService

```php
createRefund(Order $order, array $data): Refund
approveRefund(Refund $refund, Admin $admin): void
```

---

### WhatsappService

```php
createShareLink(string $phone, string $message): string
sendDocument(string $phone, string $filePath, string $caption): void
```

---

## 19. Policy / Authorization

### User

- Hanya bisa lihat order sendiri.
- Hanya bisa pakai address sendiri.
- Hanya bisa ubah profil sendiri.
- Tidak bisa akses admin endpoint.

### Admin

- Bisa akses admin endpoint.
- Bisa ubah order.
- Bisa refund.
- Bisa kelola produk.

Rekomendasi:

- Gunakan Laravel Policy untuk Order, Address, Invoice.
- Gunakan middleware role untuk admin.

---

## 20. Logging dan Audit

Log minimal untuk:

1. Checkout created
2. Payment webhook received
3. Payment status changed
4. Order status changed
5. Invoice created
6. Refund created/approved
7. Shipment sent to Bagasian
8. Admin override pricing

Gunakan:

- Laravel log channel khusus payment/order
- table `order_status_histories`
- activity log untuk admin action

---

## 21. Testing Strategy

### Unit Test

- PricingCalculator
- Voucher validation
- New user discount
- Order state transition legality
- WhatsApp link generator

### Feature Test

- Auth OTP
- Home endpoint
- Product list
- Cart CRUD
- Checkout success
- Checkout blocked without trip
- Payment webhook success
- Payment webhook duplicate
- Invoice download authorization
- Admin order status update
- Admin create invoice
- Refund flow

### Seeders

- user demo
- admin demo
- brands
- categories
- products
- banners
- trips
- vouchers
- settings

---

## 22. Maintenance Runbook

### Menambah field baru di order

1. Buat migration.
2. Update model fillable/casts.
3. Update service yang menghitung/menyimpan order.
4. Update resource.
5. Update test.
6. Update dokumentasi.

### Menambah status order

1. Tambah enum value.
2. Update transition rules di `OrderStatusService`.
3. Update history logging.
4. Update notification bila perlu.
5. Update frontend contract bila status tampil.
6. Tambah test transisi.

### Menambah payment method

1. Tambah enum value.
2. Update payment gateway adapter.
3. Update validasi checkout.
4. Update UI contract untuk payment instruction.
5. Test webhook dan UI flow.

### Menambah voucher scope shipping

1. Update voucher scope.
2. Update `PricingCalculator`.
3. Tentukan apakah voucher mengurangi invoice produk atau invoice pengiriman.
4. Update test.

### Debug webhook payment

1. Cek `webhook_events`.
2. Cek signature.
3. Cek `payments.raw_payload`.
4. Cek invoice status.
5. Cek order status history.
6. Pastikan tidak double process.

---

## 23. Catatan Penting untuk AI Agent Backend

Jika kamu AI agent yang bekerja di repo ini, ingat:

1. Jangan mengubah total berdasarkan input client.
2. Jangan buat checkout tanpa validasi trip aktif.
3. Jangan izinkan user melihat order orang lain.
4. Jangan proses webhook dua kali.
5. Jangan ubah invoice paid.
6. Jangan hapus order/invoice/payment.
7. Jangan lupa snapshot harga di order_items.
8. Jangan lupa history saat ubah status.
9. Jangan gunakan float untuk uang.
10. Jangan implementasi logic penting di controller.

---

## 24. Keputusan Desain Awal

| Keputusan | Alasan |
|---|---|
| API-first | Frontend mobile web dapat konsumsi endpoint yang sama |
| Sanctum | Auth token sederhana untuk SPA/mobile web |
| Integer money | Menghindari error floating point |
| Invoice-based payment | Mendukung pembayaran 2 tahap dan invoice tambahan |
| Order snapshot | Harga dan alamat tidak berubah setelah order |
| Shipment has many orders | Sesuai requirement 1 shipping banyak order |
| Service layer | Memudahkan maintenance dan testing |
| PDF storage | Audit dan reprint |
| WA link first | Implementasi cepat, bisa upgrade ke WA API |

---

## 25. Area yang Perlu Dikonfirmasi Sebelum Final

1. Definisi `Api alamat`.
2. Apakah user memilih trip saat checkout.
3. Payment gateway utama.
4. Aturan open PO detail.
5. Aturan refund produk hilang.
6. Biaya penanganan fixed/percent/per item/per order.
7. Apakah voucher boleh digabung dengan diskon lain.
8. Apakah ongkir pernah ditampilkan estimasi sebelum invoice 2.
9. Apakah admin dapat mengubah ongkir setelah invoice ongkir dibuat tetapi belum paid.
10. Apakah ada role CS dengan permission terbatas.