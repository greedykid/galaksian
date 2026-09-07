<!-- ========================================================= -->
<!-- SUB-VIEW: BRAND / KATEGORI FILTERED PAGE                  -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'brand-category'" class="bg-white min-h-screen space-y-4 pb-12" x-cloak>
    <!-- 1. Dedicated Top Sticky Header (Uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <button @click="closeSubView()" class="p-1 -ml-1 text-white hover:text-white/80 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span class="text-base font-extrabold text-white tracking-tight" x-text="'Aneka Produk ' + selectedFilter.name">Aneka Produk Semua Kategori</span>
        </button>
        <button @click="goToTab('cart')" class="relative p-1.5 text-white hover:text-white/80 transition cursor-pointer">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span x-show="cart && cart.total_qty > 0" class="absolute -top-1 -right-1 bg-[#00D06C] text-white font-bold text-[10px] min-w-[16px] h-4 rounded-full flex items-center justify-center px-1 tabular" x-text="cart.total_qty"></span>
        </button>
    </div>

    <!-- 2. Collection Hero Banner -->
    <div class="px-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B1E54] via-[#163E9F] to-[#1657FF] text-white p-4 shadow-sm">
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-2.5">
                <div class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-xs border border-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                    <svg class="w-3 h-3 text-amber-300 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span x-text="t('jastip_collection', 'Koleksi Jastip')">Koleksi Jastip</span>
                </div>

                <div>
                    <h2 class="text-lg font-black tracking-tight text-white leading-tight" x-text="'Koleksi ' + selectedFilter.name">Koleksi Semua Kategori</h2>
                    <p class="text-xs text-blue-100/90 mt-0.5" x-text="t('collection_desc', 'Kurasi produk pilihan siap titip dari Indonesia & Jepang')">Kurasi produk pilihan siap titip dari Indonesia & Jepang</p>
                </div>

                <div class="bg-black/30 rounded-xl p-2.5 flex items-center justify-between text-xs">
                    <span class="text-blue-100" x-text="t('total_products', 'Total produk:')">Total produk:</span>
                    <span class="font-bold text-white tabular" x-text="getFilteredSubViewProducts().length + ' ' + t('products', 'produk')"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Subtabs: Semua, Terlaris, Termurah, Promo -->
    <div class="px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <button 
                @click="selectedFilter.subtab = 'all'" 
                :class="selectedFilter.subtab === 'all' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer" x-text="t('subtab_all', 'Semua')">
                Semua
            </button>
            <button 
                @click="selectedFilter.subtab = 'bestseller'" 
                :class="selectedFilter.subtab === 'bestseller' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span x-text="t('curated_bestseller', 'Terlaris')">Terlaris</span>
            </button>
            <button 
                @click="selectedFilter.subtab = 'cheapest'" 
                :class="selectedFilter.subtab === 'cheapest' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 6l-12 12"></path><path d="M9 6h10v10"></path></svg>
                <span x-text="t('subtab_cheapest', 'Termurah')">Termurah</span>
            </button>
            <button 
                @click="selectedFilter.subtab = 'promo'" 
                :class="selectedFilter.subtab === 'promo' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                <span x-text="t('subtab_promo', 'Promo')">Promo</span>
            </button>
        </div>
    </div>

    <!-- 4. Filtered Products Grid -->
    <div class="px-4">
        <div class="grid grid-cols-2 gap-3">
            <template x-for="prod in getFilteredSubViewProducts()" :key="'cat-' + prod.id">
                <div class="bg-white border border-blue-100 rounded-2xl p-2.5 shadow-xs flex flex-col justify-between hover:border-blue-300 transition group">
                    <div>
                        <!-- Product Image + Availability Badge -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-zinc-50 cursor-pointer" @click="openProductDetail(prod)">
                            <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            
                            <span 
                                :class="prod.availability_type === 'ready_stock' ? 'bg-emerald-600 text-white' : 'bg-zinc-900 text-white'"
                                class="absolute top-2 right-2 text-[9px] font-bold px-1.5 py-0.5 rounded font-mono shadow-2xs" 
                                x-text="prod.availability_type === 'ready_stock' ? 'READY' : 'PO'">
                            </span>

                            <template x-if="prod.has_discount || (prod.discount_price && prod.discount_price < prod.price)">
                                <span class="absolute top-2 left-2 bg-[#00D06C] text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                    <span x-text="'-' + Math.round((1 - (prod.final_price || prod.discount_price) / prod.price) * 100) + '%'"></span>
                                </span>
                            </template>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 cursor-pointer hover:text-[#1657FF] leading-tight" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Price -->
                        <div class="mt-1.5">
                            <span class="text-[#00A862] font-black text-sm tabular block leading-tight" x-text="formatRupiah(prod.final_price)"></span>
                            <template x-if="prod.has_discount || prod.price > prod.final_price">
                                <span class="text-[10px] text-zinc-400 line-through tabular block" x-text="formatRupiah(prod.price)"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3 pt-2 border-t border-zinc-100">
                        <template x-if="getCartItemQty(prod.id) === 0">
                            <button 
                                @click="addToCart(prod, 1)" 
                                class="w-full py-2 bg-[#1657FF] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span x-text="t('add_to_cart_short', '+ Keranjang')">+ Keranjang</span>
                            </button>
                        </template>
                        <template x-if="getCartItemQty(prod.id) > 0">
                            <div class="flex items-center justify-between w-full bg-blue-50 border border-blue-200 rounded-xl p-1">
                                <button @click="changeCartQty(prod.id, -1)" class="w-7 h-7 bg-white border border-blue-200 rounded-lg text-blue-700 font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-blue-900 tabular" x-text="getCartItemQty(prod.id)"></span>
                                <button @click="changeCartQty(prod.id, 1)" class="w-7 h-7 bg-[#1657FF] rounded-lg text-white font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">+</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
