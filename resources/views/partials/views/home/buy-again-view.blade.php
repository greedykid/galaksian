<!-- ========================================================= -->
<!-- SUB-VIEW: BELI LAGI (REPEAT REORDER VIEW)                 -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'buy-again'" class="bg-white min-h-screen space-y-4 pb-12" x-cloak>
    <!-- 1. Dedicated Top Sticky Header (Uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center gap-3">
            <button @click="closeSubView()" class="w-9 h-9 rounded-xl border border-white/30 bg-white/15 text-white flex items-center justify-center hover:bg-white/25 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <span class="text-base font-extrabold text-white tracking-tight" x-text="t('buy_again_title', 'Beli Lagi')">Beli Lagi</span>
        </div>
        <button @click="goToTab('cart')" class="relative p-1.5 text-white hover:text-white/80 transition cursor-pointer">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span x-show="cart && cart.total_qty > 0" class="absolute -top-1 -right-1 bg-[#00D06C] text-white font-bold text-[10px] min-w-[16px] h-4 rounded-full flex items-center justify-center px-1 tabular" x-text="cart.total_qty"></span>
        </button>
    </div>

    <!-- 2. Fast Reorder Banner -->
    <div class="px-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#334155] text-white p-4 shadow-sm">
            <!-- Background Decorative Glow -->
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-500/15 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 space-y-2.5">
                <div class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-xs border border-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                    <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M8 16H3v5"></path></svg>
                    <span x-text="t('practical_reorder', 'Pemesanan Ulang Praktis')">Pemesanan Ulang Praktis</span>
                </div>

                <div>
                    <h2 class="text-lg font-black tracking-tight text-white leading-tight" x-text="t('out_of_stock_home', 'Stok Habis di Rumah?')">Stok Habis di Rumah?</h2>
                    <p class="text-xs text-slate-200 mt-0.5" x-text="t('buy_again_hero_desc', 'Beli kembali barang yang pernah kamu pesan tanpa repot mencari')">Beli kembali barang yang pernah kamu pesan tanpa repot mencari</p>
                </div>

                <div class="bg-black/30 rounded-xl p-2.5 flex items-center justify-between text-xs">
                    <span class="text-slate-300" x-text="t('total_order_history', 'Total riwayat pesanan:')">Total riwayat pesanan:</span>
                    <span class="font-bold text-white tabular" x-text="(getBuyAgainProducts().length) + ' ' + t('products', 'produk')"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Subtabs: Semua Riwayat, Sembako Rutin, Snack & Minuman, Paling Sering -->
    <div class="px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <button 
                @click="buyAgainSubtab = 'all'" 
                :class="buyAgainSubtab === 'all' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer"
                x-text="t('all_history', 'Semua Riwayat')">
                Semua Riwayat
            </button>
            <button 
                @click="buyAgainSubtab = 'sembako'" 
                :class="buyAgainSubtab === 'sembako' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path></svg>
                <span x-text="t('cat_routine_groceries', 'Sembako Rutin')">Sembako Rutin</span>
            </button>
            <button 
                @click="buyAgainSubtab = 'snack'" 
                :class="buyAgainSubtab === 'snack' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path></svg>
                <span x-text="t('cat_snacks_drinks', 'Snack & Minum')">Snack & Minum</span>
            </button>
            <button 
                @click="buyAgainSubtab = 'most-frequent'" 
                :class="buyAgainSubtab === 'most-frequent' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path></svg>
                <span x-text="t('cat_most_frequent', 'Paling Sering')">Paling Sering</span>
            </button>
        </div>
    </div>

    <!-- 4. Distinctive 1-Column Reorder Cards List Layout -->
    <div class="px-4 space-y-3">
        <template x-for="(prod, idx) in getBuyAgainProducts()" :key="'ba-view-' + prod.id">
            <div class="bg-white border border-zinc-200 rounded-2xl p-3.5 shadow-xs flex flex-col gap-3 hover:border-zinc-300 transition">
                <!-- Top Row: Thumbnail + Product Meta + Price -->
                <div class="flex items-start gap-3">
                    <!-- Thumbnail -->
                    <div class="relative w-20 h-20 rounded-xl overflow-hidden bg-zinc-50 shrink-0 cursor-pointer" @click="openProductDetail(prod)">
                        <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                    </div>

                    <!-- Meta Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full">
                                <svg class="w-2.5 h-2.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span x-text="t('bought_prefix', 'Dibeli ') + (2 + (idx % 4)) + t('times_previously', 'x sebelumnya')"></span>
                            </span>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-1 cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Last ordered date note -->
                        <p class="text-[10px] text-zinc-400 mt-0.5" x-text="t('last_ordered', 'Terakhir dibeli: ') + (14 + (idx * 2) % 15) + ' Ags 2026'"></p>

                        <!-- Bold Blue Price (#1657FF) -->
                        <div class="mt-1">
                            <span class="text-[#1657FF] font-black text-sm tabular block" x-text="formatRupiah(prod.final_price)"></span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: 1-Click Reorder Action Bar -->
                <div class="pt-2 border-t border-zinc-100">
                    <template x-if="getCartItemQty(prod.id) === 0">
                        <button 
                            @click="addToCart(prod, 1)" 
                            class="w-full py-2 bg-[#1657FF] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M8 16H3v5"></path></svg>
                            <span x-text="t('reorder_now', 'Beli Lagi Langsung')">Beli Lagi Langsung</span>
                        </button>
                    </template>
                    <template x-if="getCartItemQty(prod.id) > 0">
                        <div class="flex items-center justify-between w-full bg-blue-50 border border-blue-200 rounded-xl p-1">
                            <div class="flex items-center gap-1 px-2 text-xs font-semibold text-blue-900">
                                <svg class="w-3.5 h-3.5 text-[#00D06C] stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span x-text="t('in_cart_badge', 'Ada di Keranjang')">Ada di Keranjang</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="changeCartQty(prod.id, -1)" class="w-7 h-7 bg-white border border-blue-200 rounded-lg text-blue-700 font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-blue-900 tabular px-1" x-text="getCartItemQty(prod.id)"></span>
                                <button @click="changeCartQty(prod.id, 1)" class="w-7 h-7 bg-[#1657FF] rounded-lg text-white font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
