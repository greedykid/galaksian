# PRD Galaksian Web

## 1. Informasi Dokumen

| Item | Keterangan |
|---|---|
| Nama Produk | Galaksian Web |
| Versi Dokumen | 1.0 |
| Status | Draft Initial |
| Platform | Mobile Web |
| Framework Backend | Laravel 13 |
| Fokus Dokumen | Requirement produk, alur user, alur admin, aturan bisnis, dan kebutuhan backend |

---

## 2. Ringkasan Produk

Galaksian adalah website mobile-first untuk kebutuhan belanja/jastip produk Indonesia–Jepang. User dapat mencari produk sebelum login, memilih produk, memasukkan ke keranjang, melakukan pembayaran produk, lalu membayar ongkos kirim setelah pesanan siap diantar.

Sistem memiliki 2 sisi utama:

1. **User**
   - Auth dengan nomor handphone
   - Home dengan banner, search, toggle Indonesia–Jepang, flash sale, brand, kategori, produk rekomendasi, beli lagi, produk terlaris, promo produk
   - Keranjang
   - Checkout invoice pertama untuk produk
   - Transaksi pending dan selesai
   - Profil dan daftar alamat

2. **Admin**
   - Login admin
   - Dashboard statistik
   - CRUD produk dan import data
   - CRUD keranjang/pembayaran/status order
   - CRUD transaksi
   - CRUD profil/user
   - Kelola shipment, invoice, PDF, WhatsApp, refund

---

## 3. Tujuan Produk

1. Memudahkan user membeli produk Indonesia/Jepang melalui mobile web.
2. Mendukung proses pencarian sebelum login.
3. Mendukung pembayaran 2 tahap:
   - Invoice 1: pembayaran produk
   - Invoice 2: pembayaran ongkos kirim
4. Mendukung produk ready stock dan open PO.
5. Memberikan admin tools untuk mengelola produk, order, status, pembayaran, shipment, dan refund.
6. Menyediakan dokumen PDF untuk invoice dan pengiriman ke Bagasian.
7. Menyediakan tombol/link WhatsApp untuk CS, admin, dan pengiriman dokumen.

---

## 4. Ruang Lingkup

### 4.1 In Scope

1. Auth user menggunakan nomor handphone.
2. Halaman home:
   - navbar
   - banner carousel
   - search bar
   - switch language ID/EN
   - icon notifikasi
   - icon customer service
   - toggle Indonesia–Jepang
   - flash sale dengan countdown
   - brand 2 row
   - kategori 2 row
   - produk spesial for you
   - beli lagi
   - produk terlaris
   - promo produk
   - grid produk infinite scroll
3. Halaman produk berdasarkan brand/kategori.
4. Detail produk.
5. Keranjang.
6. Checkout invoice pertama.
7. Metode pembayaran:
   - Virtual Account
   - PayPal
   - QRIS
8. Transaksi pending.
9. Transaksi selesai.
10. Download invoice PDF.
11. Kontak penjual/admin via WhatsApp.
12. Profil user:
    - biodata
    - daftar alamat
13. CRUD alamat:
    - address
    - foto lokasi alamat
    - api alamat opsional
    - catatan pengiriman dropdown
    - nomor telepon
    - nama penerima
    - simpan alamat
14. Admin:
    - login
    - dashboard
    - CRUD produk + import
    - kelola pembayaran dan status
    - kelola transaksi
    - kelola profil/user
15. Kelola trip.
16. Kelola shipment:
    - 1 shipping id bisa memiliki banyak order id
17. Kirim PDF ke Bagasian dan WhatsApp.
18. Estimasi packing oleh admin setelah dikirim ke Bagasian.
19. Penambahan invoice baru jika user ingin menambah item/biaya.
20. Refund jika produk hilang.
21. Dukungan produk:
    - ready stock
    - open PO

### 4.2 Out of Scope

1. Aplikasi native Android/iOS.
2. Sistem saldo/wallet user.
3. Loyalty point kompleks.
4. Multi-vendor marketplace penuh.
5. Real-time tracking kurir pihak ketiga secara otomatis.
6. Sistem gudang/WMS kompleks.
7. Perpajakan kompleks, kecuali diminta kemudian.
8. Multi-currency settlement penuh. Untuk MVP, transaksi disarankan tetap menggunakan IDR.

---

## 5. Role Pengguna

| Role | Deskripsi | Akses Utama |
|---|---|---|
| Guest | Belum login | Browsing, search, lihat produk, brand, kategori |
| User | User login dengan nomor HP | Keranjang, checkout, transaksi, profil, alamat |
| Admin | Operator sistem | Kelola produk, order, transaksi, pembayaran, shipment, refund |
| Super Admin | Admin penuh | Semua akses admin + manajemen admin/role |
| CS | Customer service opsional | Lihat order, bantu user, akses WhatsApp CS |

---

## 6. Istilah Penting

| Istilah | Definisi |
|---|---|
| Trip | Jadwal/jalur pengiriman jastip, misalnya Indonesia–Jepang atau Jepang–Indonesia |
| Shipping/Shipment | Kelompok pengiriman yang dapat berisi banyak order |
| Bagasian | Pihak/proses pengiriman/packing yang menerima dokumen PDF |
| Invoice Produk | Tagihan pertama untuk pembelian produk |
| Invoice Pengiriman | Tagihan kedua untuk ongkos kirim |
| Invoice Tambahan | Invoice manual untuk tambahan biaya/item |
| Open PO | Produk bisa dipesan meskipun tidak ready stock fisik |
| Ready Stock | Produk tersedia dan dapat langsung diproses |
| Biaya Penanganan | Handling fee yang dapat dikenakan pada order |
| Sub Pengiriman Jastip | Komponen ongkir jastip |
| Sub Pengiriman Lokal | Komponen ongkir lokal |

---

## 7. Functional Requirements User

### 7.1 Auth

| ID | Requirement | Prioritas |
|---|---|---|
| AUTH-01 | User dapat registrasi/login dengan nomor handphone | P0 |
| AUTH-02 | OTP dikirim ke nomor handphone | P0 |
| AUTH-03 | OTP memiliki masa aktif dan batas percobaan | P0 |
| AUTH-04 | User dapat logout | P0 |
| AUTH-05 | Guest boleh searching dan browsing sebelum login | P0 |
| AUTH-06 | Checkout wajib login | P0 |
| AUTH-07 | Sistem dapat menandai user baru untuk promo pengguna baru | P1 |

---

### 7.2 Navbar Home

| ID | Requirement | Prioritas |
|---|---|---|
| NAV-01 | Menampilkan teks Galaksian, klik untuk redirect/refresh ke home | P0 |
| NAV-02 | Menampilkan icon CS yang redirect ke WhatsApp/modal CS | P0 |
| NAV-03 | Menampilkan switch language ID/EN | P1 |
| NAV-04 | Menampilkan icon notifikasi dengan modal cepat | P1 |
| NAV-05 | Menampilkan search bar | P0 |
| NAV-06 | Search mencari berdasarkan nama produk, brand, kategori | P0 |
| NAV-07 | Menampilkan banner carousel | P0 |
| NAV-08 | Banner dapat diklik sesuai CTA/link yang diatur admin | P0 |
| NAV-09 | Menampilkan toggle Indonesia–Jepang | P0 |

Aturan toggle:

- Jika toggle hijau/Indonesia, tampilkan produk dari Indonesia.
- Jika toggle merah/Jepang, tampilkan produk dari luar negeri/Jepang.
- Toggle dapat mempengaruhi daftar produk dan/atau kurs tampilan.

---

### 7.3 Main Home

| ID | Requirement | Prioritas |
|---|---|---|
| HOME-01 | Menampilkan flash sale dengan countdown | P1 |
| HOME-02 | Flash sale tampil 1 row carousel horizontal | P1 |
| HOME-03 | Produk flash sale memiliki control qty `-` dan `+` | P1 |
| HOME-04 | Menampilkan brand 2 row tanpa carousel | P0 |
| HOME-05 | Brand berisi contoh brand Indonesia seperti Indofood, Dua Kelinci, Nestle | P0 |
| HOME-06 | Menampilkan kategori 2 row | P0 |
| HOME-07 | Kategori berisi seperti makanan instan, bumbu dapur, minuman, bahan masakan | P0 |
| HOME-08 | Menampilkan produk Special For You | P1 |
| HOME-09 | Special For You dapat berupa rekomendasi acak/berdasarkan brand/kategori | P1 |
| HOME-10 | Menampilkan Beli Lagi berdasarkan riwayat pembelian user | P1 |
| HOME-11 | Menampilkan Produk Terlaris | P1 |
| HOME-12 | Menampilkan Promo Produk dengan harga coret | P1 |
| HOME-13 | Menampilkan grid produk 4 kolom dengan infinite scroll | P0 |
| HOME-14 | Home dapat terdiri lebih dari satu page/section | P0 |

---

### 7.4 Halaman Setelah Klik Brand/Kategori

| ID | Requirement | Prioritas |
|---|---|---|
| BRK-01 | Menampilkan seluruh produk berdasarkan brand/kategori yang dipilih | P0 |
| BRK-02 | Search bar tetap tersedia | P0 |
| BRK-03 | Tab/filter dapat disesuaikan dengan brand/kategori | P1 |
| BRK-04 | Judul halaman berubah mengikuti brand/kategori, contoh: “Aneka Produk Indofood” | P1 |
| BRK-05 | User dapat menambah qty dengan icon `+` dan mengurangi dengan icon `-` | P0 |

---

### 7.5 Detail Produk

| ID | Requirement | Prioritas |
|---|---|---|
| PDP-01 | Menampilkan gambar produk | P0 |
| PDP-02 | Menampilkan nama produk | P0 |
| PDP-03 | Menampilkan harga | P0 |
| PDP-04 | Menampilkan harga promo bila ada | P1 |
| PDP-05 | Menampilkan deskripsi produk | P0 |
| PDP-06 | Menampilkan stok/label stok | P0 |
| PDP-07 | Menampilkan control qty | P0 |
| PDP-08 | Menampilkan tombol tambah ke keranjang | P0 |
| PDP-09 | Produk dapat memiliki tipe ready stock atau open PO | P0 |

---

### 7.6 Keranjang

| ID | Requirement | Prioritas |
|---|---|---|
| CART-01 | Menampilkan produk yang dipilih user | P0 |
| CART-02 | Menampilkan qty per item | P0 |
| CART-03 | Menampilkan harga total | P0 |
| CART-04 | Menampilkan total qty | P0 |
| CART-05 | Menampilkan suggest produk | P2 |
| CART-06 | User dapat memasukkan promo pengguna baru atau promo lainnya | P1 |
| CART-07 | Total ongkir ditiadakan/dihapus pada tampilan keranjang | P0 |
| CART-08 | User dapat mengubah qty | P0 |
| CART-09 | User dapat menghapus item | P0 |
| CART-10 | Keranjang menjadi pintu masuk checkout | P0 |

---

### 7.7 Checkout

| ID | Requirement | Prioritas |
|---|---|---|
| CH-01 | Checkout diakses dari tombol bayar di keranjang | P0 |
| CH-02 | Menampilkan invoice produk | P0 |
| CH-03 | Menampilkan pemilihan metode pembayaran | P0 |
| CH-04 | Metode pembayaran mendukung Virtual Account, PayPal, dan QRIS | P0 |
| CH-05 | Pembayaran memiliki jangka waktu/expiry | P0 |
| CH-06 | Total ongkos kirim dihapus/tidak ditampilkan pada checkout pertama | P0 |
| CH-07 | User memilih alamat pengiriman | P0 |
| CH-08 | Checkout hanya bisa dilakukan jika ada trip aktif | P0 |
| CH-09 | Sistem membuat invoice pertama untuk produk | P0 |

---

### 7.8 After Klik Bayar Sekarang

| ID | Requirement | Prioritas |
|---|---|---|
| PAY-01 | Jika pembayaran berhasil, tampilan status menjadi hijau | P0 |
| PAY-02 | Muncul popup notifikasi pembayaran berhasil | P0 |
| PAY-03 | User di-redirect ke transaksi dengan status Pending | P0 |
| PAY-04 | Jika pembayaran belum berhasil, status tetap pending | P0 |
| PAY-05 | Jika pembayaran expired, sistem menangani status invoice/order | P0 |

---

### 7.9 Transaksi Pending

| ID | Requirement | Prioritas |
|---|---|---|
| TRX-P-01 | Menampilkan transaksi yang masih berjalan | P0 |
| TRX-P-02 | Menampilkan alamat pengiriman | P0 |
| TRX-P-03 | Menampilkan qty yang dibeli | P0 |
| TRX-P-04 | Menampilkan jumlah harga yang telah/sedang dibayarkan | P0 |
| TRX-P-05 | Menampilkan status pesanan | P0 |
| TRX-P-06 | User dapat download invoice PDF | P0 |
| TRX-P-07 | Terdapat tombol/icon hubungi penjual yang redirect ke WhatsApp admin | P0 |
| TRX-P-08 | Transaksi pending mencakup pembayaran pertama dan pembayaran kedua untuk ongkir | P0 |
| TRX-P-09 | Button order ulang ditiadakan selama status belum siap diantar | P0 |
| TRX-P-10 | Jika status sudah siap diantar, user perlu melakukan pembayaran kedua untuk ongkir | P0 |

---

### 7.10 Transaksi Selesai

| ID | Requirement | Prioritas |
|---|---|---|
| TRX-S-01 | Menampilkan histori pembelian yang telah selesai | P0 |
| TRX-S-02 | User dapat melakukan review | P1 |
| TRX-S-03 | User dapat membeli lagi | P1 |
| TRX-S-04 | User dapat share | P2 |
| TRX-S-05 | Tampilan dibuat mobile friendly | P0 |

---

### 7.11 Profil

| ID | Requirement | Prioritas |
|---|---|---|
| PRF-01 | Profil memiliki tab biodata diri dan daftar alamat | P0 |
| PRF-02 | Biodata dapat berisi nama, nomor telepon, KTP opsional, email opsional | P0 |
| PRF-03 | User dapat mengubah biodata | P0 |
| PRF-04 | User dapat mengubah kata sandi jika menggunakan password | P1 |
| PRF-05 | Tampilan mobile friendly | P0 |

---

### 7.12 Daftar Alamat

| ID | Requirement | Prioritas |
|---|---|---|
| ADDR-01 | User dapat CRUD alamat | P0 |
| ADDR-02 | User dapat menyimpan lebih dari satu alamat | P0 |
| ADDR-03 | Field alamat: Address | P0 |
| ADDR-04 | Field alamat: Foto lokasi alamat | P0 |
| ADDR-05 | Field alamat: Api alamat opsional | P1 |
| ADDR-06 | Field alamat: Catatan pengiriman dropdown | P1 |
| ADDR-07 | Field alamat: Nomor telepon | P0 |
| ADDR-08 | Field alamat: Nama penerima | P0 |
| ADDR-09 | User dapat menyimpan alamat sebagai default | P0 |
| ADDR-10 | Tampilan mobile friendly | P0 |

---

## 8. Functional Requirements Admin

### 8.1 Login Admin

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-AUTH-01 | Admin dapat login | P0 |
| ADM-AUTH-02 | Admin memiliki role terpisah dari user | P0 |
| ADM-AUTH-03 | Session/token admin aman | P0 |

---

### 8.2 Dashboard Admin

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-DASH-01 | Menampilkan statistik produk | P1 |
| ADM-DASH-02 | Menampilkan statistik rekomendasi | P2 |
| ADM-DASH-03 | Menampilkan statistik order/transaksi | P1 |
| ADM-DASH-04 | Menampilkan statistik pembayaran | P1 |
| ADM-DASH-05 | Menampilkan statistik user bila diperlukan | P2 |

---

### 8.3 CRUD Produk

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-PRD-01 | Admin dapat create, read, update, delete produk | P0 |
| ADM-PRD-02 | Admin dapat import data produk | P1 |
| ADM-PRD-03 | Admin dapat mengatur harga normal dan harga promo | P0 |
| ADM-PRD-04 | Admin dapat mengatur stock | P0 |
| ADM-PRD-05 | Admin dapat mengatur produk ready stock/open PO | P0 |
| ADM-PRD-06 | Admin dapat mengatur gambar produk | P0 |
| ADM-PRD-07 | Admin dapat mengatur brand dan kategori | P0 |
| ADM-PRD-08 | Admin dapat mengatur flash sale | P1 |
| ADM-PRD-09 | Admin dapat mengaktifkan/nonaktifkan produk | P0 |

---

### 8.4 CRUD Keranjang/Pembayaran/Status

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-ORD-01 | Admin dapat melihat keranjang/order user | P0 |
| ADM-ORD-02 | Admin dapat melihat alur pembayaran | P0 |
| ADM-ORD-03 | Admin dapat melihat status pembayaran | P0 |
| ADM-ORD-04 | Admin dapat mengubah status order user yang telah membeli barang | P0 |
| ADM-ORD-05 | Admin dapat mencatat history perubahan status | P0 |

---

### 8.5 CRUD Transaksi

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-TRX-01 | Admin dapat melihat daftar transaksi | P0 |
| ADM-TRX-02 | Admin dapat melihat detail transaksi | P0 |
| ADM-TRX-03 | Admin dapat melihat invoice | P0 |
| ADM-TRX-04 | Admin dapat membuat invoice tambahan | P1 |
| ADM-TRX-05 | Admin dapat memproses refund | P0 |

---

### 8.6 CRUD Profil/User

| ID | Requirement | Prioritas |
|---|---|---|
| ADM-USR-01 | Admin dapat melihat daftar user | P1 |
| ADM-USR-02 | Admin dapat melihat detail profil user | P1 |
| ADM-USR-03 | Admin dapat mengubah data user bila diperlukan | P1 |
| ADM-USR-04 | Admin dapat menonaktifkan user bila diperlukan | P1 |

---

## 9. Requirement Trip & Order

| ID | Requirement | Prioritas |
|---|---|---|
| TRIP-01 | User tidak bisa langsung pesan sebelum ada trip yang tersedia | P0 |
| TRIP-02 | Admin dapat mengelola trip | P0 |
| TRIP-03 | Trip memiliki status aktif/nonaktif | P0 |
| TRIP-04 | Checkout harus memvalidasi trip aktif | P0 |
| TRIP-05 | Trip dapat memiliki rute Indonesia–Jepang atau Jepang–Indonesia | P0 |

---

## 10. Requirement Shipment & Bagasian

| ID | Requirement | Prioritas |
|---|---|---|
| SHP-01 | 1 shipping id bisa memiliki banyak order id/keranjang | P0 |
| SHP-02 | Admin dapat membuat shipment | P0 |
| SHP-03 | Admin dapat assign order ke shipment | P0 |
| SHP-04 | Admin dapat mengirim format PDF ke Bagasian | P1 |
| SHP-05 | Tampilan/PDF dapat dikirim ke WhatsApp sehingga whatsapp-able | P1 |
| SHP-06 | Estimasi packing ditentukan admin setelah dikirim ke Bagasian | P1 |
| SHP-07 | Admin dapat mengisi sub pengiriman jastip dan sub pengiriman lokal | P0 |
| SHP-08 | Admin dapat mengubah status pengiriman | P0 |

---

## 11. Struktur Harga

Rincian harga produk yang harus didukung:

1. Sub total produk
2. Total pengiriman (belum fix)
3. Biaya penanganan
4. Promo
5. Diskon pengguna baru
6. Voucher
7. Total tagihan produk
8. Pengiriman produk:
   - Sub pengiriman jastip
   - Sub pengiriman lokal

### Formula Awal

```text
product_subtotal = sum(harga produk * qty)

product_discount = promo + diskon pengguna baru + voucher

product_total = product_subtotal - product_discount + biaya penanganan

shipping_total = sub_pengiriman_jastip + sub_pengiriman_lokal

grand_total = product_total + shipping_total
```

Catatan penting:

- Total pengiriman pada invoice pertama belum fix dan tidak ditagihkan dulu.
- Invoice pertama hanya untuk total tagihan produk.
- Invoice kedua untuk pengiriman produk.
- Semua perhitungan dilakukan di backend.
- Nominal uang disimpan sebagai integer.

---

## 12. Invoice

### Jenis Invoice

| Jenis | Keterangan |
|---|---|
| product | Invoice pertama untuk pembayaran produk |
| shipping | Invoice kedua untuk ongkos kirim |
| additional | Invoice tambahan bila user ingin menambah item/biaya |

### Aturan Invoice

1. Invoice produk dibuat saat checkout.
2. Invoice pengiriman dibuat ketika status pesanan siap diantar atau admin memicu invoice ongkir.
3. Invoice tambahan dapat dibuat admin.
4. Invoice yang sudah paid tidak boleh diubah nominalnya secara langsung.
5. Jika ada perubahan biaya setelah invoice paid, gunakan invoice tambahan atau refund.
6. Invoice dapat diunduh sebagai PDF.

---

## 13. Alur Pesanan Utama

### Alur User

1. User membuka home.
2. User search produk.
3. User menentukan qty.
4. Produk masuk ke keranjang.
5. User memilih metode pembayaran untuk invoice 1.
6. User membayar.
7. Sistem memverifikasi pembayaran produk.
8. Admin memproses pesanan.
9. Shipping status menjadi siap diantar.
10. Invoice 2 muncul untuk ongkos kirim.
11. User memilih metode pembayaran.
12. User membayar ongkir.
13. Pesanan dikirim.
14. Pesanan selesai.

### Alur Admin

1. Admin melihat order masuk.
2. Admin memvalidasi pembayaran produk.
3. Admin memproses order.
4. Admin assign order ke shipment.
5. Admin mengirim PDF ke Bagasian/WhatsApp.
6. Admin menerima/mengisi estimasi packing.
7. Admin mengisi sub ongkir jastip dan lokal.
8. Admin mengubah status menjadi siap diantar.
9. Sistem/admin membuat invoice pengiriman.
10. Admin memantau pembayaran ongkir.
11. Admin memproses pengiriman.
12. Admin menandai transaksi selesai.

---

## 14. Unhappy Case

### 14.1 Refund Apabila Produk Hilang

| ID | Requirement | Prioritas |
|---|---|---|
| REF-01 | Admin dapat menandai produk/order hilang | P0 |
| REF-02 | Admin dapat membuat refund | P0 |
| REF-03 | Refund dapat full atau partial sesuai keputusan bisnis | P0 |
| REF-04 | Refund dicatat dalam history | P0 |
| REF-05 | User mendapat notifikasi refund | P1 |
| REF-06 | Order tidak dihapus, tetapi status menjadi refund/refunded | P0 |

### 14.2 Pembayaran Expired

| ID | Requirement | Prioritas |
|---|---|---|
| EXP-01 | Invoice memiliki expired_at | P0 |
| EXP-02 | Jika invoice produk expired, order dapat dibatalkan atau kembali editable | P0 |
| EXP-03 | Jika invoice ongkir expired, admin dapat membuat invoice baru | P1 |

### 14.3 Produk Habis

| ID | Requirement | Prioritas |
|---|---|---|
| STK-01 | Produk ready stock tidak bisa checkout jika stock habis | P0 |
| STK-02 | Produk open PO dapat tetap dipesan sesuai aturan PO | P0 |

---

## 15. Business Rules

| ID | Rule |
|---|---|
| BR-01 | Guest boleh searching sebelum login |
| BR-02 | Checkout membutuhkan login |
| BR-03 | User tidak bisa pesan sebelum ada trip aktif |
| BR-04 | 1 shipping id dapat berisi banyak order id |
| BR-05 | Total ongkir tidak ditampilkan pada keranjang/checkout pertama |
| BR-06 | Invoice pertama hanya untuk produk |
| BR-07 | Invoice kedua untuk ongkir setelah status siap diantar |
| BR-08 | Estimasi packing ditentukan admin setelah dikirim ke Bagasian |
| BR-09 | Biaya penanganan dapat ditambahkan ke total tagihan produk |
| BR-10 | Promo, diskon pengguna baru, dan voucher dihitung backend |
| BR-11 | Invoice paid tidak boleh diubah nominal |
| BR-12 | Perubahan status order harus dicatat |
| BR-13 | Produk hilang dapat memicu refund |
| BR-14 | Produk open PO memiliki aturan stock/PO berbeda dari ready stock |
| BR-15 | Semua total harga dihitung di backend |

---

## 16. Data Model High Level

Entitas utama:

1. `users`
2. `addresses`
3. `products`
4. `brands`
5. `categories`
6. `banners`
7. `trips`
8. `carts`
9. `cart_items`
10. `orders`
11. `order_items`
12. `shipments`
13. `invoices`
14. `payments`
15. `vouchers`
16. `refunds`
17. `reviews`
18. `notifications`
19. `otp_codes`
20. `order_status_histories`
21. `webhook_events`
22. `settings`

---

## 17. Status Order

| Status | Keterangan |
|---|---|
| draft | Checkout belum selesai |
| pending_payment_product | Menunggu pembayaran invoice produk |
| paid_product | Invoice produk sudah dibayar |
| processing | Admin memproses order |
| packing | Sedang packing |
| ready_for_delivery | Siap diantar, invoice ongkir dapat ditagihkan |
| pending_payment_shipping | Menunggu pembayaran ongkir |
| shipping_paid | Ongkir sudah dibayar |
| delivering | Sedang dikirim |
| completed | Selesai |
| cancelled | Dibatalkan |
| refund_requested | Pengajuan refund |
| refunded | Refund selesai |

---

## 18. Status Invoice

| Status | Keterangan |
|---|---|
| pending | Belum dibayar |
| paid | Sudah dibayar |
| expired | Kadaluarsa |
| failed | Gagal |
| refunded | Dana dikembalikan |

---

## 19. API Surface Ringkas

### User

```text
POST /api/v1/auth/otp/request
POST /api/v1/auth/otp/verify
POST /api/v1/auth/logout
GET  /api/v1/me
PUT  /api/v1/me

GET  /api/v1/home
GET  /api/v1/products
GET  /api/v1/products/{slug}
GET  /api/v1/brands
GET  /api/v1/categories
GET  /api/v1/brands/{slug}/products
GET  /api/v1/categories/{slug}/products

GET  /api/v1/cart
POST /api/v1/cart/items
PATCH /api/v1/cart/items/{id}
DELETE /api/v1/cart/items/{id}
POST /api/v1/cart/voucher

POST /api/v1/checkout
GET  /api/v1/orders
GET  /api/v1/orders/{id}
POST /api/v1/orders/{id}/invoices/{invoiceId}/pay
GET  /api/v1/orders/{id}/invoices/{invoiceId}/download

GET  /api/v1/addresses
POST /api/v1/addresses
PUT  /api/v1/addresses/{id}
DELETE /api/v1/addresses/{id}

POST /api/v1/orders/{id}/review
```

### Admin

```text
POST /api/v1/admin/auth/login
GET  /api/v1/admin/dashboard

GET/POST/PUT/DELETE /api/v1/admin/products
POST /api/v1/admin/products/import

GET/POST/PUT/DELETE /api/v1/admin/brands
GET/POST/PUT/DELETE /api/v1/admin/categories
GET/POST/PUT/DELETE /api/v1/admin/banners

GET/POST/PUT /api/v1/admin/trips
GET/POST/PUT /api/v1/admin/shipments

GET  /api/v1/admin/orders
GET  /api/v1/admin/orders/{id}
PATCH /api/v1/admin/orders/{id}/status
POST /api/v1/admin/orders/{id}/assign-shipment
POST /api/v1/admin/orders/{id}/invoices
POST /api/v1/admin/shipments/{id}/send-bagasian

GET  /api/v1/admin/users
GET  /api/v1/admin/users/{id}
PUT  /api/v1/admin/users/{id}

POST /api/v1/admin/refunds
```

### Webhook

```text
POST /api/v1/webhooks/payment
```

---

## 20. Non-Functional Requirements

| Area | Requirement |
|---|---|
| Security | OTP hashed, rate limit, webhook signature, role middleware |
| Performance | Pagination untuk product list, cache untuk home bila perlu |
| Reliability | Webhook idempotent, payment tidak double-process |
| Auditability | History status order dan admin action |
| Maintainability | Business logic di service layer |
| Localization | Mendukung ID/EN |
| Mobile-first | API mendukung UI mobile sticky footer |
| Storage | Upload foto alamat dan dokumen PDF tervalidasi |
| Observability | Log payment, webhook, refund, status order |
| Data integrity | Data transaksi tidak di-hard delete sembarangan |

---

## 21. Asumsi Awal

1. Backend menggunakan Laravel 13.
2. Frontend mengkonsumsi REST API.
3. Auth user menggunakan OTP nomor handphone.
4. Pembayaran menggunakan payment gateway yang mendukung VA, QRIS, PayPal, atau adapter terpisah.
5. WhatsApp tahap awal dapat menggunakan `wa.me` link.
6. Bagasian dapat berupa proses manual PDF + WhatsApp sebelum ada API resmi.
7. Mata uang transaksi utama adalah IDR.
8. Total pengiriman belum fix pada invoice pertama.
9. Admin dapat menentukan ongkir setelah packing/Bagasian.
10. Invoice tambahan dapat dibuat manual oleh admin.

---

## 22. Open Questions

1. Apa maksud field `Api alamat`? Apakah koordinat, kode API wilayah, atau referensi eksternal?
2. Apakah user memilih trip saat checkout, atau admin yang assign trip?
3. Payment gateway apa yang akan digunakan?
4. Apakah PayPal wajib untuk MVP?
5. WhatsApp menggunakan `wa.me` atau WhatsApp Business API?
6. Apakah Bagasian memiliki API resmi?
7. Biaya penanganan bersifat fixed, percent, per item, atau per order?
8. Apakah voucher bisa digabung dengan promo produk dan diskon pengguna baru?
9. Diskon pengguna baru berlaku untuk order pertama paid atau order pertama created?
10. Produk open PO memiliki kuota atau unlimited sampai trip cutoff?
11. Refund produk hilang apakah full, partial, termasuk ongkir atau tidak?
12. Apakah invoice ongkir boleh dibuat otomatis saat status ready_for_delivery?
13. Apakah perlu role CS terpisah?
14. Apakah notifikasi perlu email/push, atau cukup in-app?
15. Apakah KTP wajib disimpan? Jika ya, perlu encryption.

---

## 23. MVP Priority

### P0

- Auth OTP
- Home & product list
- Detail produk
- Keranjang
- Checkout invoice produk
- Payment webhook
- Transaksi pending/selesai
- Address CRUD
- Admin product CRUD
- Admin order status
- Admin shipment assign
- Invoice PDF
- Refund basic
- Trip validation

### P1

- Flash sale
- Beli lagi
- Produk terlaris
- Promo produk
- Voucher
- Diskon user baru
- Import produk
- Dashboard admin
- WhatsApp Business API
- Review
- Bahasa EN

### P2

- Recommendation engine lanjutan
- Share tracking
- Loyalty
- Multi-language content penuh
- Statistik dashboard lanjutan