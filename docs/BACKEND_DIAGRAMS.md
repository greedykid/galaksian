# 📊 Diagram Arsitektur & Alur Bisnis Galaksian

> Dokumen ini memuat diagram visual lengkap arsitektur backend Galaksian menggunakan format **Mermaid**. GitHub dan Markdown viewer modern mendukung rendering langsung diagram ini.

---

## 📌 Daftar Diagram
1. [Arsitektur Aliran Kerja Backend (Request Lifecycle)](#1-arsitektur-aliran-kerja-backend-request-lifecycle)
2. [Alur Transaksi Jastip 2 Tahap (Inovasi Inti)](#2-alur-transaksi-jastip-2-tahap-inovasi-inti)
3. [Siklus Hidup Status Pesanan (Order State Machine)](#3-siklus-hidup-status-pesanan-order-state-machine)
4. [Peta Hubungan Data di Database (ERD Sederhana)](#4-peta-hubungan-data-di-database-erd-sederhana)
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
        DB[(MySQL Database)]
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

## 4. Peta Hubungan Data di Database (ERD Sederhana)

Diagram relasi entitas berikut memperlihatkan bagaimana tabel-tabel utama di basis data saling berelasi:

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "membuat banyak"
    USERS ||--o{ ADDRESSES : "memiliki banyak alamat"
    USERS ||--|| CARTS : "memiliki satu keranjang aktif"
    
    TRIPS ||--o{ ORDERS : "menampung belanjaan"
    SHIPMENTS ||--o{ ORDERS : "mengelompokkan ke bagasi"
    
    CARTS ||--o{ CART_ITEMS : "berisi item"
    PRODUCTS ||--o{ CART_ITEMS : "dipilih ke"
    PRODUCTS ||--o{ ORDER_ITEMS : "dijadikan snapshot"

    ORDERS ||--|{ ORDER_ITEMS : "terdiri dari barang"
    ORDERS ||--|{ INVOICES : "memiliki tagihan (Produk/Ongkir/Tambahan)"
    ORDERS ||--o{ ORDER_STATUS_HISTORIES : "mencatat jejak perubahan status"
    ORDERS ||--o{ REFUNDS : "memiliki pengembalian dana"

    INVOICES ||--o{ PAYMENTS : "memiliki riwayat bayar (QRIS/VA)"
    WEBHOOK_EVENTS ||--o{ PAYMENTS : "memverifikasi idempotensi"
```

---

## 5. Alur Webhook Pembayaran Idempotent (Anti Bayar Dobel)

Diagram alur penanganan notifikasi pembayaran dari payment gateway agar bebas dari risiko dobel transaksi:

```mermaid
flowchart TD
    Hook["Payment Gateway Mengirim Webhook POST"] --> CheckEvent{"Apakah event_id Sudah Ada di webhook_events?"}

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
