<!-- ========================================================= -->
<!-- SUB-VIEW: FLASH SALE & BUNDLE HEMAT DIASPORA              -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'flash-sale'" class="bg-white min-h-screen space-y-4 pb-12" x-cloak>
    <!-- 1. Dedicated Top Sticky Header (Uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center gap-3">
            <button @click="closeSubView()" class="w-9 h-9 rounded-xl border border-white/30 bg-white/15 text-white flex items-center justify-center hover:bg-white/25 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <span class="text-base font-extrabold text-white tracking-tight">Flash Sale & Bundle</span>
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

    <!-- 2. High-Impact Live Urgency Hero Banner -->
    <div class="px-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B1E54] via-[#163E9F] to-[#1657FF] text-white p-4 shadow-sm">
            <!-- Background Decorative Blur Circle -->
            <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-red-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 bg-red-600/90 text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider shadow-xs">
                        <svg class="w-3 h-3 text-amber-300 fill-current animate-pulse" viewBox="0 0 24 24">
                            <path d="M12 2c1.1 0 2 .9 2 2 0 .7-.4 1.4-1 1.7V7c1.7.5 3 2.1 3 4 0 1.7-1 3.2-2.5 3.8.3.7.5 1.4.5 2.2 0 2.8-2.2 5-5 5s-5-2.2-5-5c0-1.2.4-2.3 1.1-3.2C3.8 12.9 3 11.5 3 10c0-2.2 1.8-4 4-4 .3 0 .7 0 1 .1V5.7C7.4 5.4 7 4.7 7 4c0-1.1.9-2 2-2 1.7 0 3 1.3 3 3v.1c0-.1 0-.1 0 0z"/>
                        </svg>
                        <span x-text="t('lightning_today', 'Kilat Hari Ini')">Kilat Hari Ini</span>
                    </div>
                    <span class="text-[11px] text-blue-100/90 font-medium" x-text="t('limited_batch_quota', 'Kuota Kloter Terbatas')">Kuota Kloter Terbatas</span>
                </div>

                <div>
                    <h2 class="text-lg font-black tracking-tight text-white leading-tight" x-text="t('flash_sale_hero_title', 'Diskon Kilat Diaspora s/d 70%')">Diskon Kilat Diaspora s/d 70%</h2>
                    <p class="text-xs text-blue-100/80 mt-0.5" x-text="t('flash_sale_hero_desc', 'Stok terbatas untuk titipan kiriman langsung dari Jepang')">Stok terbatas untuk titipan kiriman langsung dari Jepang</p>
                </div>

                <!-- Live Countdown Digital Boxes -->
                <div class="bg-black/30 backdrop-blur-xs rounded-xl p-2.5 border border-white/10 flex items-center justify-between">
                    <span class="text-xs text-zinc-200 font-semibold" x-text="t('ends_in', 'Berakhir dalam:')">Berakhir dalam:</span>
                    <div class="flex items-center gap-1.5 font-mono">
                        <div class="flex flex-col items-center">
                            <span class="bg-red-600 text-white font-bold text-xs px-2 py-1 rounded-md shadow-xs tabular" x-text="flashSaleCountdown.h">02</span>
                            <span class="text-[8px] text-zinc-300 uppercase mt-0.5" x-text="t('hours', 'Jam')">Jam</span>
                        </div>
                        <span class="text-white font-bold text-sm -mt-2.5">:</span>
                        <div class="flex flex-col items-center">
                            <span class="bg-red-600 text-white font-bold text-xs px-2 py-1 rounded-md shadow-xs tabular" x-text="flashSaleCountdown.m">45</span>
                            <span class="text-[8px] text-zinc-300 uppercase mt-0.5" x-text="t('mins', 'Mnt')">Mnt</span>
                        </div>
                        <span class="text-white font-bold text-sm -mt-2.5">:</span>
                        <div class="flex flex-col items-center">
                            <span class="bg-red-600 text-white font-bold text-xs px-2 py-1 rounded-md shadow-xs tabular" x-text="flashSaleCountdown.s">08</span>
                            <span class="text-[8px] text-zinc-300 uppercase mt-0.5" x-text="t('secs', 'Dtk')">Dtk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Subtabs: Semua, Diskon >40%, Paket Bundle, Di Bawah Rp 100rb -->
    <div class="px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <button 
                @click="flashSaleSubtab = 'all'" 
                :class="flashSaleSubtab === 'all' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer">
                <span x-text="t('all_promos', 'Semua Promo')">Semua Promo</span>
            </button>
            <button 
                @click="flashSaleSubtab = 'huge-discount'" 
                :class="flashSaleSubtab === 'huge-discount' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 text-red-500 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span x-text="t('discount_over_40', 'Diskon >40%')">Diskon >40%</span>
            </button>
            <button 
                @click="flashSaleSubtab = 'bundles'" 
                :class="flashSaleSubtab === 'bundles' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                <span x-text="t('bundle_packages', 'Paket Bundle')">Paket Bundle</span>
            </button>
            <button 
                @click="flashSaleSubtab = 'under100k'" 
                :class="flashSaleSubtab === 'under100k' ? 'bg-[#1657FF] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer">
                <span x-text="t('under_100k', '< Rp 100rb')">&lt; Rp 100rb</span>
            </button>
        </div>
    </div>


    <!-- 4. Flash Sale Scarcity Products Grid -->
    <div class="px-4">
        <div class="grid grid-cols-2 gap-3">
            <template x-for="(prod, idx) in getFlashSaleProducts()" :key="'fs-view-' + prod.id">
                <div class="bg-white border border-red-100 rounded-2xl p-2.5 shadow-xs flex flex-col justify-between hover:border-red-300 transition group relative">
                    <div>
                        <!-- Product Image + Big Discount Pill & Scarcity Badge -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-zinc-50 cursor-pointer" @click="openProductDetail(prod)">
                            <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            
                            <!-- Red Discount Pill -->
                            <span class="absolute top-2 left-2 bg-[#E60012] text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                <span x-text="prod.has_discount ? ('-' + Math.round((1 - prod.final_price / prod.price) * 100) + '%') : ('-' + (40 + (idx * 5) % 30) + '%')"></span>
                            </span>

                            <!-- Availability Stock Badge -->
                            <span 
                                :class="prod.availability_type === 'ready_stock' ? 'bg-emerald-600 text-white' : 'bg-zinc-900 text-white'"
                                class="absolute top-2 right-2 text-[9px] font-bold px-1.5 py-0.5 rounded font-mono shadow-2xs" 
                                x-text="prod.availability_type === 'ready_stock' ? 'READY' : 'PO'">
                            </span>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 cursor-pointer hover:text-[#1657FF] leading-tight" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Pricing -->
                        <div class="mt-1.5">
                            <span class="text-[#00A862] font-black text-sm tabular block leading-tight" x-text="formatRupiah(prod.final_price)"></span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(prod.price || (prod.final_price * 1.6))"></span>
                                <span class="text-[9px] font-extrabold text-red-600 bg-red-50 px-1 py-0.2 rounded" x-text="t('save_badge', 'HEMAT')">HEMAT</span>
                            </div>
                        </div>

                        <!-- Scarcity Stock Meter -->
                        <div class="mt-2">
                            <div class="w-full bg-zinc-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-red-500 h-full rounded-full" :style="'width: ' + (75 + ((idx * 7) % 23)) + '%'"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-zinc-500 mt-1 font-medium">
                                <span class="flex items-center gap-0.5 text-red-600 font-semibold">
                                    <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2 0 .7-.4 1.4-1 1.7V7c1.7.5 3 2.1 3 4 0 1.7-1 3.2-2.5 3.8.3.7.5 1.4.5 2.2 0 2.8-2.2 5-5 5s-5-2.2-5-5c0-1.2.4-2.3 1.1-3.2C3.8 12.9 3 11.5 3 10c0-2.2 1.8-4 4-4 .3 0 .7 0 1 .1V5.7C7.4 5.4 7 4.7 7 4c0-1.1.9-2 2-2 1.7 0 3 1.3 3 3v.1c0-.1 0-.1 0 0z"/></svg>
                                    <span x-text="t('remaining_prefix', 'Tersisa ') + (2 + (idx % 6)) + ' ' + t('pcs', 'pcs')"></span>
                                </span>
                                <span class="text-zinc-400" x-text="(prod.stock ? (prod.stock * 3 + 40) : (180 + idx * 12)) + ' ' + t('sold', 'terjual')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3 pt-2 border-t border-zinc-100">
                        <template x-if="getCartItemQty(prod.id) === 0">
                            <button 
                                @click="addToCart(prod, 1)" 
                                class="w-full py-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span x-text="t('claim_discount_btn', 'Ambil Diskon')">Ambil Diskon</span>
                            </button>
                        </template>
                        <template x-if="getCartItemQty(prod.id) > 0">
                            <div class="flex items-center justify-between w-full bg-red-50 border border-red-200 rounded-xl p-1">
                                <button @click="changeCartQty(prod.id, -1)" class="w-7 h-7 bg-white border border-red-200 rounded-lg text-red-600 font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-red-700 tabular" x-text="getCartItemQty(prod.id)"></span>
                                <button @click="changeCartQty(prod.id, 1)" class="w-7 h-7 bg-red-600 rounded-lg text-white font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">+</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
