# 📊 Diagram Arsitektur & Alur Bisnis Galaksian

> Dokumen ini memuat diagram visual lengkap arsitektur backend Galaksian menggunakan format **Mermaid**. GitHub dan Markdown viewer modern mendukung rendering langsung diagram ini.

---

## 📌 Daftar Diagram
1. [Arsitektur Aliran Kerja Backend (Request Lifecycle)](#1-arsitektur-aliran-kerja-backend-request-lifecycle)
2. [Alur Transaksi Jastip 2 Tahap (Inovasi Inti)](#2-alur-transaksi-jastip-2-tahap-inovasi-inti)
3. [Siklus Hidup Status Pesanan (Order State Machine)](#3-siklus-hidup-status-pesanan-order-state-machine)
4. [Diagram Relasi Entitas Lengkap (Complete Database ERD)](#4-diagram-relasi-entitas-lengkap-complete-database-erd)
   - [4.1 Master ERD: Seluruh Entitas & Kolom Lengkap](#41-master-erd-seluruh-entitas--kolom-lengkap)
   - [4.2 Sub-ERD: Domain Transaksi & Jastip 2 Tahap](#42-sub-erd-domain-transaksi--jastip-2-tahap)
   - [4.3 Sub-ERD: Domain Pengguna & Otentikasi](#43-sub-erd-domain-pengguna--otentikasi)
   - [4.4 Sub-ERD: Domain Katalog & Keranjang](#44-sub-erd-domain-katalog--keranjang)
5. [Alur Webhook Pembayaran Idempotent (Anti Bayar Dobel)](#5-alur-webhook-pembayaran-idempotent-anti-bayar-dobel)

---

## 1. Arsitektur Aliran Kerja Backend (Request Lifecycle)

Diagram ini memperlihatkan bagaimana permintaan (*request*) dari layar HP pengguna diproses langkah demi langkah oleh backend hingga masuk ke database:

```mermaid
flowchart TD
    subgraph Frontend["Layar Pengguna (Frontend)"]
        UI["Aplikasi Web Mobile (Alpine.js / Tailwind)"]
    end

    subgraph EntryPoint["1. Gerbang Masuk API"]
        Route["Routing (routes/api.php & routes/admin.php)"]
        FormReq["Form Request (Validasi Input & Format No HP)"]
    end

    subgraph ControllerLayer["2. Pengarah Tugas (Tipis)"]
        Ctrl["API Controller (Hanya Menerima & Memanggil Service)"]
    end

    subgraph ServiceLayer["3. Dapur Utama (Business Logic)"]
        Pricing["PricingCalculator (Hitung Subtotal, Diskon, Voucher, Fee)"]
        Checkout["CheckoutService (Kunci Stok & Buat Pesanan)"]
        OrderState["OrderStatusService (Aturan Perubahan Status Order)"]
        PaymentService["PaymentGatewayService (Urus QRIS, VA, & Webhook)"]
        OrderOps["OrderService (Solusi Barang Habis / Skema B)"]
        ShipmentServ["ShipmentService (Koper, Koli, & Manifest Bagasian)"]
    end

    subgraph Database["4. Basis Data (ACID Transaction)"]
        DB[(PostgreSQL Database)]
        Lock["Pessimistic Lock (lockForUpdate: Cegah Rebutan Stok)"]
    end

    subgraph External["5. Layanan Luar"]
        PGateway["Payment Gateway (QRIS / Bank)"]
        PDF["PDF Generator (Invoice & Bagasian)"]
        WA["WhatsApp (Notifikasi Pesanan)"]
    end

    UI -->|"Kirim Data (JSON)"| Route
    Route --> FormReq
    FormReq --> Ctrl
    Ctrl --> ServiceLayer

    Checkout --> Pricing
    Checkout --> Lock
    Lock --> DB
    PaymentService -->|"Terima Webhook"| DB
    OrderState --> DB
    OrderOps --> DB
    ShipmentServ --> DB

    PaymentService <--> PGateway
    ShipmentServ --> PDF
    OrderState --> WA
    Ctrl -->|"Kirim Respon (JSON Berstandar)"| UI
```

---

## 2. Alur Transaksi Jastip 2 Tahap (Inovasi Inti)

Diagram ini menjelaskan mengapa ada dua tahap penagihan (Invoice Produk di awal, lalu Invoice Ongkir setelah barang ditimbang di Indonesia):

```mermaid
flowchart TD
    Start([Pembeli Checkout di Keranjang]) --> CekTrip{"Apakah Ada Trip Aktif ke Jepang?"}
    
    CekTrip -- "Tidak Ada" --> Gagal["Tolak Checkout (Wajib ada Traveler)"]
    CekTrip -- "Ada Trip" --> HitungHarga["Backend Hitung Ulang Harga di Server"]
    
    subgraph Tahap1["TAHAP 1: Tagihan Produk"]
        HitungHarga --> BuatOrder["Buat Order (pending_payment_product)"]
        BuatOrder --> Inv1["Terbitkan Invoice 1: Produk + Handling Fee"]
        Inv1 --> BayarInv1["Pembeli Membayar via QRIS / VA"]
        BayarInv1 --> Webhook1["Webhook Gateway Memvalidasi Transfer"]
        Webhook1 --> Lunas1["Status: Lunas Produk (paid_product)"]
    end

    subgraph ProsesJepang["Fase Pembelanjaan di Tokyo"]
        Lunas1 --> Belanja["Traveler Membelikan Barang di Toko Jepang"]
        Belanja --> CekStok{"Barang Ada di Toko?"}
        CekStok -- "Ada" --> Packing["Barang Dikemas ke Koper (Shipment)"]
        CekStok -- "Habis (OOS)" --> OOS["Skema B: Ganti Barang / Refund Auto-Offset"]
        OOS --> Packing
    end

    subgraph Tahap2["TAHAP 2: Tagihan Pengiriman"]
        Packing --> Timbang["Traveler Tiba di Indonesia & Timbang Berat Riil"]
        Timbang --> Inv2["Terbitkan Invoice 2: Ongkir Jastip + Kurir Lokal"]
        Inv2 --> BayarInv2["Pembeli Membayar Tagihan Ongkir"]
        BayarInv2 --> Lunas2["Status: Ongkir Lunas (shipping_paid)"]
    end

    Lunas2 --> Kirim["Paket Diserahkan ke Kurir Lokal"]
    Kirim --> Selesai([Pesanan Diterima Pembeli / Completed])
```

---

## 3. Siklus Hidup Status Pesanan (Order State Machine)

Diagram ini menunjukkan transisi status pesanan yang dikontrol secara ketat oleh `OrderStatusService.php`. Status tidak boleh melompat secara ilegal:

```mermaid
graph TD
    draft([Draft]) --> pending_prod[Pending Payment Product]
    pending_prod --> paid_prod[Paid Product / Lunas Produk]
    pending_prod --> cancelled[Cancelled]
    
    paid_prod --> processing[Processing / Belanja di Jepang]
    processing --> packing[Packing / Dikemas]
    packing --> ready[Ready For Delivery / Siap Kirim]
    
    ready --> pending_ship[Pending Payment Shipping / Tagihan Ongkir]
    pending_ship --> ship_paid[Shipping Paid / Ongkir Lunas]
    
    ship_paid --> delivering[Delivering / Dalam Pengiriman Kurir]
    delivering --> completed([Completed / Pesanan Selesai])
    
    paid_prod -.-> refund_req[Refund Requested]
    processing -.-> refund_req
    refund_req --> refunded([Refunded])
```

---

## 4. Diagram Relasi Entitas Lengkap (Complete Database ERD)

### 4.1 Master ERD: Seluruh Entitas & Kolom Lengkap

Diagram ini memuat **seluruh 22 tabel basis data Galaksian** lengkap dengan tipe data, Primary Key (`PK`), Foreign Key (`FK`), Unique Key (`UK`), serta kardinalitas relasi yang presisi:

```mermaid
erDiagram
    %% ========================================================
    %% 1. PENGGUNA, OTENTIKASI & PROFIL
    %% ========================================================
    users {
        bigint id PK
        string name "Nama Lengkap"
        string phone UK "Nomor Handphone E.164"
        string email UK "Email (Opsional)"
        string password "Hashed Password"
        string role "user / admin"
        string language "id / en"
        boolean is_new_user "Penanda Pengguna Baru"
        timestamp new_user_promo_used_at "Waktu Promo Pertama Digunakan"
        timestamp last_login_at "Waktu Login Terakhir"
        timestamp created_at
        timestamp deleted_at "Soft Delete"
    }

    otp_codes {
        bigint id PK
        string phone "Nomor Handphone Tujuan"
        string code_hash "Bcrypt Hash 6 Digit OTP"
        string purpose "login / verify"
        timestamp expires_at "Kedaluwarsa (5 Menit)"
        integer attempts "Jumlah Percobaan (Max 3)"
        timestamp consumed_at "Waktu OTP Dipakai"
        string ip_address "Alamat IP Klien"
        timestamp created_at
    }

    addresses {
        bigint id PK
        bigint user_id FK "Relasi ke users"
        string label "Rumah, Kantor, dll"
        string recipient_name "Nama Penerima"
        string phone "No Handphone Penerima"
        text address "Alamat Lengkap"
        string photo_path "Foto Patokan / Pagar Rumah"
        string api_address "Alamat Terstandar API"
        string delivery_note "Instruksi Kurir"
        boolean is_default "Alamat Default Pengiriman"
        string country "ID / JP"
        string province "Provinsi"
        string city "Kota / Kabupaten"
        string district "Kecamatan"
        string postal_code "Kode Pos"
        timestamp created_at
    }

    notifications {
        bigint id PK
        bigint user_id FK "Relasi ke users"
        string type "order / payment / refund / promo"
        string title "Judul Notifikasi"
        text body "Pesan Lengkap"
        json data "Metadata Tambahan"
        timestamp read_at "Waktu Dibaca"
        timestamp created_at
    }

    %% ========================================================
    %% 2. KATALOG PRODUK, BRAND, KATEGORI & KERANJANG
    %% ========================================================
    brands {
        bigint id PK
        string name "Nama Brand (Uniqlo, Donki, dll)"
        string slug UK "URL Slug Brand"
        string logo_path "Path Gambar Logo"
        boolean is_active "Status Tampil"
        timestamp created_at
    }

    categories {
        bigint id PK
        string name "Nama Kategori"
        string slug UK "URL Slug Kategori"
        string image_path "Path Gambar Kategori"
        boolean is_active "Status Tampil"
        timestamp created_at
    }

    products {
        bigint id PK
        string sku UK "Kode Unik SKU"
        string slug UK "URL Slug Produk"
        string name "Nama Produk Jastip"
        text description "Deskripsi Detail"
        bigint brand_id FK "Relasi ke brands"
        bigint category_id FK "Relasi ke categories"
        string origin_country "JP / ID"
        string currency "IDR / JPY"
        bigint price "Harga Asli (Integer Rupiah)"
        bigint discount_price "Harga Diskon (Opsional)"
        integer stock "Stok Barang Fisik"
        integer low_stock_threshold "Batas Warning Stok"
        string availability_type "ready_stock / open_po"
        boolean is_active "Status Aktif Produk"
        boolean is_flash_sale "Status Flash Sale"
        timestamp flash_sale_start_at
        timestamp flash_sale_end_at
        integer weight_gram "Berat Estimasi dalam Gram"
        timestamp created_at
        timestamp deleted_at "Soft Delete"
    }

    product_images {
        bigint id PK
        bigint product_id FK "Relasi ke products"
        string image_path "Path Berkas Foto"
        boolean is_primary "Foto Utama Display"
        integer order "Urutan Tampil Galeri"
        timestamp created_at
    }

    carts {
        bigint id PK
        bigint user_id FK "Relasi ke users (Unique)"
        string status "active / checked_out / abandoned"
        timestamp created_at
    }

    cart_items {
        bigint id PK
        bigint cart_id FK "Relasi ke carts"
        bigint product_id FK "Relasi ke products"
        integer qty "Jumlah Item"
        timestamp created_at
    }

    vouchers {
        bigint id PK
        string code UK "Kode Kupon Diskon"
        string type "fixed / percent"
        bigint value "Nominal Diskon (Rp / %)"
        bigint max_discount "Maks Diskon (Jika %)"
        bigint min_order_amount "Min Belanja"
        integer usage_limit "Batas Total Kuota"
        integer usage_per_user "Batas Kuota Per User"
        string applicable_scope "product / shipping / all"
        timestamp starts_at "Mulai Berlaku"
        timestamp ends_at "Batas Berlaku"
        boolean is_active "Status Aktif Voucher"
        timestamp created_at
    }

    banners {
        bigint id PK
        string title "Judul Banner"
        string image_path "Path Gambar Banner"
        string link_url "Tautan Promosi"
        string cta_text "Teks Tombol Aksi"
        integer order "Urutan Display"
        boolean is_active "Status Tampil"
        string locale "id / en"
        string country_filter "all / JP / ID"
        timestamp created_at
    }

    %% ========================================================
    %% 3. OPERASIONAL JASTIP (TRIP & BAGASIAN SHIPMENT)
    %% ========================================================
    trips {
        bigint id PK
        string code UK "Kode Trip (TRIP-2026-09-JP)"
        string origin_country "ID (Indonesia)"
        string destination_country "JP (Jepang)"
        timestamp departure_at "Jadwal Keberangkatan"
        timestamp arrival_at "Jadwal Kepulangan"
        timestamp cutoff_at "Batas Waktu Order"
        string status "draft / active / ongoing / completed"
        text notes "Catatan Rute Belanja"
        timestamp created_at
    }

    shipments {
        bigint id PK
        string shipment_number UK "Nomor Koli / Bagasi"
        bigint trip_id FK "Relasi ke trips"
        string status "draft / in_transit / arrived / delivered"
        string origin_country "JP"
        string destination_country "ID"
        string bagasian_reference "Ref Bea Cukai / AWB"
        string packing_estimate_weight "Estimasi Berat Koli (kg)"
        string packing_estimate_volume "Dimensi Koper / Koli"
        bigint packing_estimate_cost "Biaya Bagasi Pesawat"
        timestamp sent_to_bagasian_at "Waktu Dispatch Bagasi"
        string bagasian_pdf_path "Path PDF Manifest Bea Cukai"
        text wa_message "Log Pesan WA Broadcast"
        text notes "Catatan Khusus Koper"
        timestamp created_at
    }

    %% ========================================================
    %% 4. TRANSAKSI, INVOICE, PEMBAYARAN & AUDIT
    %% ========================================================
    orders {
        bigint id PK
        string order_number UK "Nomor Order (ORD-202609-XXXX)"
        bigint user_id FK "Relasi ke users"
        bigint trip_id FK "Relasi ke trips"
        bigint shipment_id FK "Relasi ke shipments"
        bigint voucher_id FK "Relasi ke vouchers (Opsional)"
        string status "pending_payment_product / paid_product / etc"
        string currency "IDR"
        bigint exchange_rate "Kurs Mata Uang"
        json address_snapshot "Salinan Alamat Saat Checkout"
        bigint product_subtotal "Subtotal Produk Asli"
        bigint product_discount_amount "Total Diskon Promo"
        bigint new_user_discount_amount "Diskon Pembeli Baru"
        bigint voucher_amount "Nominal Diskon Voucher"
        bigint handling_fee_amount "Biaya Penanganan Sistem"
        bigint product_total "Tagihan Tahap 1 (Produk)"
        bigint shipping_jastip_amount "Ongkir Bagasi JP->ID"
        bigint shipping_local_amount "Ongkir Kurir Lokal ID->ID"
        bigint shipping_total "Tagihan Tahap 2 (Pengiriman)"
        bigint grand_total "Total Transaksi Keseluruhan"
        text notes "Catatan Tambahan Pembeli"
        timestamp product_paid_at "Waktu Lunas Produk"
        timestamp shipping_paid_at "Waktu Lunas Ongkir"
        timestamp completed_at "Waktu Selesai"
        timestamp cancelled_at "Waktu Batal"
        string cancel_reason "Alasan Pembatalan"
        timestamp created_at
        timestamp deleted_at "Soft Delete"
    }

    order_items {
        bigint id PK
        bigint order_id FK "Relasi ke orders"
        bigint product_id FK "Relasi ke products"
        string product_name_snapshot "Nama Produk Saat Dibeli"
        string brand_name_snapshot "Nama Brand Saat Dibeli"
        string sku_snapshot "Kode SKU Saat Dibeli"
        integer qty "Jumlah Kuantitas"
        bigint unit_price "Harga Satuan Setelah Promo"
        bigint original_price "Harga Satuan Asli Master"
        bigint discount_amount "Potongan Diskon Satuan"
        bigint subtotal "Subtotal (unit_price x qty)"
        string availability_type "ready_stock / open_po"
        string refund_status "none / requested / refunded"
        bigint refund_amount "Nominal Refund (Jika OOS)"
        timestamp created_at
    }

    invoices {
        bigint id PK
        string invoice_number UK "Nomor Invoice (INV-202609-XXXX)"
        bigint order_id FK "Relasi ke orders"
        string type "product / shipping / additional"
        string status "pending / paid / expired / failed / refunded"
        bigint amount "Nominal Tagihan (Integer Rupiah)"
        text description "Uraian Rincian Tagihan"
        string payment_method "qris / virtual_account / paypal"
        string payment_gateway "midtrans / xendit / manual"
        string gateway_reference "ID Transaksi Gateway"
        timestamp paid_at "Waktu Terkonfirmasi Lunas"
        timestamp expired_at "Batas Waktu Bayar"
        bigint created_by "Admin ID / System"
        json payload "Data Gateway Terkait"
        timestamp created_at
    }

    payments {
        bigint id PK
        bigint invoice_id FK "Relasi ke invoices"
        string method "qris / bca_va / mandiri_va / paypal"
        string status "pending / paid / failed / expired / refund"
        bigint amount "Nominal Uang Masuk"
        string gateway_reference "Referensi Transaksi Bank"
        json raw_payload "Log Respon Gateway Mentah"
        timestamp paid_at "Waktu Dana Diterima"
        timestamp failed_at
        timestamp refunded_at
        timestamp created_at
    }

    refunds {
        bigint id PK
        bigint order_id FK "Relasi ke orders"
        bigint invoice_id FK "Relasi ke invoices (Opsional)"
        text reason "Alasan Refund (Barang Habis / OOS)"
        bigint amount "Nominal Pengembalian Dana"
        string status "pending / approved / rejected / completed"
        bigint approved_by "Admin ID yang Menyetujui"
        string refund_method "bank_transfer / auto_offset"
        string evidence_path "Bukti Transfer Refund"
        timestamp created_at
    }

    order_status_histories {
        bigint id PK
        bigint order_id FK "Relasi ke orders"
        string from_status "Status Asal"
        string to_status "Status Tujuan"
        text note "Keterangan Perubahan Status"
        string actor_type "user / admin / system"
        bigint actor_id "ID Pengubah Status"
        timestamp created_at
    }

    reviews {
        bigint id PK
        bigint user_id FK "Relasi ke users"
        bigint product_id FK "Relasi ke products"
        bigint order_id FK "Relasi ke orders"
        tinyint rating "Bintang 1-5"
        text comment "Ulasan Pembeli"
        json images "Foto Review Produk"
        string status "published / hidden"
        timestamp created_at
    }

    webhook_events {
        bigint id PK
        string source "midtrans / xendit / paypal"
        string event_id UK "ID Event Unik Gateway"
        string event_type "payment.captured / invoice.paid"
        json payload "Payload Mentah Webhook"
        string signature "HMAC Signature Keamanan"
        string status "received / processed / failed"
        timestamp processed_at "Waktu Selesai Diproses"
        timestamp created_at
    }

    settings {
        string key PK "Kunci Setting (handling_fee, dll)"
        text value "Nilai Pengaturan Sistem"
        timestamp created_at
    }

    %% ========================================================
    %% 5. RELASI KARDINALITAS LENGKAP
    %% ========================================================
    users ||--o{ addresses : "memiliki alamat"
    users ||--|| carts : "memiliki satu keranjang"
    users ||--o{ orders : "membuat pesanan"
    users ||--o{ reviews : "menulis ulasan"
    users ||--o{ notifications : "menerima pesan"

    carts ||--o{ cart_items : "berisi rincian item"
    products ||--o{ cart_items : "dimasukkan ke"
    
    brands ||--o{ products : "memproduksi"
    categories ||--o{ products : "mengelompokkan"
    products ||--o{ product_images : "memiliki foto galeri"
    products ||--o{ order_items : "disalin jadi snapshot"
    products ||--o{ reviews : "diulas dalam"

    trips ||--o{ shipments : "membawa koper bagasi"
    trips ||--o{ orders : "menampung titipan belanja"
    shipments ||--o{ orders : "mengelompokkan pesanan"
    vouchers ||--o{ orders : "diterapkan pada"

    orders ||--|{ order_items : "terdiri dari barang belanja"
    orders ||--|{ invoices : "menerbitkan nota tagihan"
    orders ||--o{ order_status_histories : "mencatat jejak status"
    orders ||--o{ refunds : "memiliki pengembalian dana"
    orders ||--o{ reviews : "dinilai oleh pembeli"

    invoices ||--o{ payments : "dibayar dengan bukti"
    invoices ||--o{ refunds : "dikompensasi via"
```

---

### 4.2 Sub-ERD: Domain Transaksi & Jastip 2 Tahap

Fokus khusus pada bagaimana pesanan belanja jastip diproses: pemisahan tagihan produk dan ongkir (*2-stage invoicing*), penugasan koper traveler (*shipment*), jejak audit status (*histories*), serta kompensasi pengembalian dana (*refund auto-offset*):

```mermaid
erDiagram
    TRIPS ||--o{ ORDERS : "menampung titipan"
    TRIPS ||--o{ SHIPMENTS : "membawa koper"
    SHIPMENTS ||--o{ ORDERS : "mengelompokkan paket"
    
    ORDERS ||--|{ ORDER_ITEMS : "rincian barang"
    ORDERS ||--|{ INVOICES : "tagihan 2 tahap"
    ORDERS ||--o{ ORDER_STATUS_HISTORIES : "jejak status"
    ORDERS ||--o{ REFUNDS : "pengembalian dana"

    INVOICES ||--o{ PAYMENTS : "bukti bayar"
    INVOICES ||--o{ REFUNDS : "kompensasi offset"

    ORDERS {
        bigint id PK
        string order_number UK
        bigint user_id FK
        bigint trip_id FK
        bigint shipment_id FK
        string status
        bigint product_total "Tagihan Tahap 1"
        bigint shipping_total "Tagihan Tahap 2"
        bigint grand_total
        json address_snapshot
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string product_name_snapshot
        integer qty
        bigint unit_price
        bigint subtotal
        string availability_type
        string refund_status
        bigint refund_amount
    }

    INVOICES {
        bigint id PK
        string invoice_number UK
        bigint order_id FK
        string type "product / shipping / additional"
        string status "pending / paid / expired"
        bigint amount "Nominal Rupiah"
        string payment_method
        string gateway_reference
        timestamp paid_at
    }

    PAYMENTS {
        bigint id PK
        bigint invoice_id FK
        string method "qris / va / paypal"
        string status "paid / pending / failed"
        bigint amount
        string gateway_reference
        timestamp paid_at
    }

    REFUNDS {
        bigint id PK
        bigint order_id FK
        bigint invoice_id FK "Offset Target"
        bigint amount
        string status "approved / completed"
        string refund_method "bank_transfer / auto_offset"
    }

    ORDER_STATUS_HISTORIES {
        bigint id PK
        bigint order_id FK
        string from_status
        string to_status
        text note
        string actor_type "user / admin / system"
    }
```

---

### 4.3 Sub-ERD: Domain Pengguna & Otentikasi

Fokus pada entitas akun pembeli, otentikasi login OTP WhatsApp/SMS tanpa password, buku alamat penerima dengan foto patokan, dan sistem notifikasi:

```mermaid
erDiagram
    USERS ||--o{ ADDRESSES : "memiliki banyak alamat"
    USERS ||--o{ NOTIFICATIONS : "menerima pesan"
    USERS ||--o{ REVIEWS : "memberi ulasan"

    USERS {
        bigint id PK
        string name "Nama Lengkap"
        string phone UK "No Handphone Utama"
        string email UK
        string role "user / admin"
        string language "id / en"
        boolean is_new_user "Hak Diskon Pembeli Baru"
        timestamp created_at
    }

    OTP_CODES {
        bigint id PK
        string phone "No Handphone Pemohon"
        string code_hash "Bcrypt Hash 6 Digit"
        string purpose "login"
        integer attempts "Max 3 Percobaan"
        timestamp expires_at "5 Menit"
        timestamp consumed_at "Waktu Terpakai"
    }

    ADDRESSES {
        bigint id PK
        bigint user_id FK
        string recipient_name "Nama Penerima"
        string phone "No HP Penerima"
        text address "Alamat Rumah"
        string photo_path "Foto Pagar/Patokan Rumah"
        string delivery_note "Instruksi Kurir"
        boolean is_default "Alamat Utama"
        string city
        string postal_code
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        string type "order / payment / refund"
        string title
        text body
        timestamp read_at
    }
```

---

### 4.4 Sub-ERD: Domain Katalog & Keranjang

Fokus pada katalog produk Jepang/Indonesia, relasi brand, kategori, galeri foto, keranjang aktif pembeli, dan kupon voucher diskon:

```mermaid
erDiagram
    BRANDS ||--o{ PRODUCTS : "memproduksi"
    CATEGORIES ||--o{ PRODUCTS : "mengelompokkan"
    PRODUCTS ||--o{ PRODUCT_IMAGES : "foto galeri"
    
    CARTS ||--o{ CART_ITEMS : "daftar barang dipilih"
    PRODUCTS ||--o{ CART_ITEMS : "dipilih ke keranjang"

    BRANDS {
        bigint id PK
        string name "Uniqlo, Donki, Muji"
        string slug UK
        string logo_path
        boolean is_active
    }

    CATEGORIES {
        bigint id PK
        string name "Snack, Fashion, Kosmetik"
        string slug UK
        string image_path
        boolean is_active
    }

    PRODUCTS {
        bigint id PK
        string sku UK
        string slug UK
        string name
        bigint brand_id FK
        bigint category_id FK
        string origin_country "JP / ID"
        bigint price "Harga Normal"
        bigint discount_price "Harga Promo"
        integer stock "Stok Fisik"
        string availability_type "ready_stock / open_po"
        integer weight_gram "Berat Barang"
        boolean is_active
    }

    PRODUCT_IMAGES {
        bigint id PK
        bigint product_id FK
        string image_path
        boolean is_primary
        integer order
    }

    CARTS {
        bigint id PK
        bigint user_id FK "Unique per user"
        string status "active / checked_out"
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        integer qty
    }

    VOUCHERS {
        bigint id PK
        string code UK "Kupon Diskon"
        string type "fixed / percent"
        bigint value
        bigint min_order_amount
        boolean is_active
    }
```

---

## 5. Alur Webhook Pembayaran Idempotent (Anti Bayar Dobel)

Kontrol keamanan webhook saat ini:

- Production wajib mengaktifkan signature atau callback token.
- `event_id`/`id` wajib unik untuk idempotency.
- Source webhook harus dikenal.
- Status pembayaran dan nominal wajib valid serta nominal harus sama dengan invoice.
- Invoice dan event dikunci dalam transaksi.
- Event duplikat serta invoice yang sudah paid tidak diproses ulang.

Diagram alur penanganan notifikasi pembayaran dari payment gateway agar bebas dari risiko dobel transaksi:

```mermaid
flowchart TD
    Hook["Payment Gateway Mengirim Webhook POST"] --> Verify{"Validasi signature, source, status, dan nominal"}
    Verify -->|Invalid| Reject["Tolak webhook"]
    Verify -->|Valid| CheckEvent{"Apakah event_id Sudah Ada di webhook_events?"}

    CheckEvent -- "SUDAH ADA (Duplikat)" --> Ignore["Abaikan & Return HTTP 200 (Idempotent Safe)"]
    
    CheckEvent -- "BELUM ADA" --> RecordLock["Catat event_id dengan status 'processing'"]
    RecordLock --> FindInv["Cari Invoice berdasarkan invoice_number"]
    FindInv --> PayInv["Update Status Invoice -> PAID"]
    PayInv --> CreatePayment["Catat Record Payment -> PAID"]
    
    CreatePayment --> CheckType{"Tipe Invoice?"}
    CheckType -- "Product" --> SetPaidProd["Ubah Status Order -> paid_product"]
    CheckType -- "Shipping" --> SetPaidShip["Ubah Status Order -> shipping_paid"]
    CheckType -- "Additional" --> SetPaidAdd["Tandai Tagihan Tambahan Lunas"]
    
    SetPaidProd --> LogHist["Catat ke order_status_histories (actor: payment_gateway)"]
    SetPaidShip --> LogHist
    SetPaidAdd --> LogHist
    
    LogHist --> MarkDone["Update status webhook_events -> 'processed'"]
    MarkDone --> ResponseOK["Return HTTP 200 (Success)"]
```
