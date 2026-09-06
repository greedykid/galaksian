<!-- ========================================================= -->
<!-- FLOATING CART BAR (REFERENCE: media_1788731539605.png)     -->
<!-- Tampil mengambang di atas bottom-nav saat bukan di cart    -->
<!-- ========================================================= -->
<div 
    x-show="cart && cart.total_qty > 0 && activeTab !== 'cart' && activeSubView !== 'checkout' && activeSubView !== 'payment-instruction'"
    x-transition:enter="transition ease-out duration-200 transform"
    x-transition:enter-start="opacity-0 translate-y-3 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150 transform"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-3 scale-95"
    class="fixed bottom-[66px] inset-x-0 mx-auto z-40 w-full max-w-[430px] px-3.5 pointer-events-none"
    x-cloak>
    
    <div 
        @click="goToTab('cart')" 
        class="pointer-events-auto cursor-pointer bg-white rounded-2xl border border-zinc-200/90 shadow-[0_8px_30px_rgba(0,0,0,0.12)] p-2.5 px-3.5 flex items-center justify-between hover:border-emerald-300 transition active:scale-[0.99] group">
        
        <!-- Sisi Kiri: Icon Keranjang Hijau Muda + Badge Qty + Jumlah Barang + Total Rupiah -->
        <div class="flex items-center gap-3">
            <!-- Icon Container (Kotak Rounded Hijau Muda) -->
            <div class="relative w-11 h-11 rounded-2xl bg-[#E8F8F0] flex items-center justify-center shrink-0 border border-emerald-100/80">
                <!-- Tas Belanja / Keranjang Hitam Solid Sesuai Referensi -->
                <svg class="w-5 h-5 text-zinc-900 fill-current" viewBox="0 0 24 24">
                    <path d="M16 6V4a4 4 0 0 0-8 0v2H4a1 1 0 0 0-1 1v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V7a1 1 0 0 0-1-1h-4zm-6-2a2 2 0 0 1 4 0v2h-4V4zm9 15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8h14v11z"/>
                </svg>
                <!-- Badge Hijau Bulat di Pojok Kanan Atas dengan Border Putih -->
                <span 
                    class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 bg-[#00D06C] text-white font-black text-[11px] px-1 rounded-full flex items-center justify-center shadow-xs border-2 border-white tabular" 
                    x-text="cart.total_qty">
                </span>
            </div>

            <!-- Teks Jumlah Barang & Total Harga Hijau Tebal -->
            <div class="flex flex-col">
                <span class="text-xs font-black text-[#0B1E54] tracking-tight leading-tight" x-text="(cart.total_qty || 0) + ' Barang'"></span>
                <span class="text-sm font-black text-[#00A862] tabular leading-tight mt-0.5" x-text="formatRupiah(getCartTotalAmount())"></span>
            </div>
        </div>

        <!-- Sisi Kanan: Teks "Lihat Keranjang" + Tombol Bulat Hijau Panah Kanan -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-extrabold text-[#00A862] group-hover:text-[#008f53] transition" x-text="t('view_cart', 'Lihat Keranjang')">Lihat Keranjang</span>
            <div class="w-8 h-8 rounded-full bg-[#00D06C] group-hover:bg-[#00B85F] text-white flex items-center justify-center shadow-xs transition active:scale-95 shrink-0">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </div>
        </div>

    </div>
</div>

<!-- ========================================================= -->
<!-- STICKY BOTTOM NAVIGATION BAR (FIXED & RESILIENT)          -->
<!-- ========================================================= -->
<nav x-show="activeSubView !== 'checkout' && activeSubView !== 'payment-instruction'" class="fixed bottom-0 inset-x-0 mx-auto z-40 w-full max-w-[430px] bg-white border-t border-x border-zinc-200 px-3 py-2 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.04)]" x-cloak>
    <!-- 1. Home / Beranda -->
    <button 
        @click="goToTab('home')" 
        :class="(activeTab === 'home' || ['brand-category', 'flash-sale', 'indonesia-catalog', 'special-for-you', 'buy-again'].includes(activeSubView)) ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
        class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition cursor-pointer">
        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span class="text-[10px] tracking-tight" x-text="t('nav_home', 'Beranda')">Beranda</span>
    </button>

    <!-- 2. Cart / Keranjang + Live Count -->
    <button 
        @click="goToTab('cart')" 
        :class="activeTab === 'cart' && !activeSubView ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
        class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition relative cursor-pointer">
        <div class="relative">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                <path d="M3 6h18"></path>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <!-- Live Item Count Badge -->
            <template x-if="cart.total_qty > 0">
                <span class="absolute -top-1.5 -right-2.5 bg-[#00D06C] text-white text-[9px] font-black px-1.5 py-0.2 rounded-full tabular shadow-2xs" x-text="cart.total_qty"></span>
            </template>
        </div>
        <span class="text-[10px] tracking-tight" x-text="t('nav_cart', 'Keranjang')">Keranjang</span>
    </button>

    <!-- 3. Transactions / Transaksi -->
    <button 
        @click="goToTab('transactions')" 
        :class="(activeTab === 'transactions' || activeSubView === 'order-detail' || activeSubView === 'qris-payment') ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
        class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition cursor-pointer">
        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
            <path d="M14 8H8"></path>
            <path d="M16 12H8"></path>
            <path d="M13 16H8"></path>
        </svg>
        <span class="text-[10px] tracking-tight" x-text="t('nav_transactions', 'Transaksi')">Transaksi</span>
    </button>

    <!-- 4. Profile / Profil -->
    <button 
        @click="goToTab('profile')" 
        :class="activeTab === 'profile' && !activeSubView ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
        class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition cursor-pointer">
        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span class="text-[10px] tracking-tight" x-text="t('nav_profile', 'Profil')">Profil</span>
    </button>
</nav>
