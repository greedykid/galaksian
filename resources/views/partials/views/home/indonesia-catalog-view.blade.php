<!-- ========================================================= -->
<!-- SUB-VIEW: KATALOG PRODUK INDONESIA                        -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'indonesia-catalog'" class="bg-white min-h-screen space-y-4 pb-12" x-cloak>
    <!-- 1. Dedicated Top Sticky Header (Uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <button @click="closeSubView()" class="p-1 -ml-1 text-white hover:text-white/80 transition flex items-center gap-2 cursor-pointer">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <span class="text-base font-extrabold text-white tracking-tight">Katalog Produk Indonesia</span>
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

    <!-- 2. Warm Diaspora Pantry Hero Banner -->
    <div class="px-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#064E3B] via-[#047857] to-[#0D9488] text-white p-4 shadow-sm">
            <!-- Background Decorative Glow -->
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-400/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 space-y-2.5">
                <div class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-xs border border-white/20 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                    <svg class="w-3 h-3 text-amber-300 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>Otentik Nusantara di Jepang</span>
                </div>

                <div>
                    <h2 class="text-lg font-black tracking-tight text-white leading-tight">Pantry & Cita Rasa Indonesia</h2>
                    <p class="text-xs text-emerald-100/90 mt-0.5">Sembako, bumbu racik, sambal, hingga jamu tradisional siap kirim</p>
                </div>

                <!-- 3 Guarantees Pills -->
                <div class="grid grid-cols-3 gap-1.5 pt-1 text-[10px]">
                    <div class="bg-black/20 rounded-lg p-1.5 text-center flex flex-col items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-300 mb-0.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span class="font-bold leading-tight">100% Asli</span>
                    </div>
                    <div class="bg-black/20 rounded-lg p-1.5 text-center flex flex-col items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-300 mb-0.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span class="font-bold leading-tight">Halal & BPOM</span>
                    </div>
                    <div class="bg-black/20 rounded-lg p-1.5 text-center flex flex-col items-center">
                        <svg class="w-3.5 h-3.5 text-emerald-300 mb-0.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                        <span class="font-bold leading-tight">Aman ke JP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Category Filter Horizontal Scrollable Badges (Clean SVG Icons, NO Emojis) -->
    <div class="px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <!-- Semua -->
            <button 
                @click="catalogSubtab = 'all'" 
                :class="catalogSubtab === 'all' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer">
                Semua
            </button>
            <!-- Mie & Sembako -->
            <button 
                @click="catalogSubtab = 'mie-sembako'" 
                :class="catalogSubtab === 'mie-sembako' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                <span>Mie & Sembako</span>
            </button>
            <!-- Kopi & Teh -->
            <button 
                @click="catalogSubtab = 'kopi-teh'" 
                :class="catalogSubtab === 'kopi-teh' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M17 8h1a4 4 0 1 1 0 8h-1"></path><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"></path><line x1="6" y1="2" x2="6" y2="4"></line><line x1="10" y1="2" x2="10" y2="4"></line><line x1="14" y1="2" x2="14" y2="4"></line></svg>
                <span>Kopi & Teh</span>
            </button>
            <!-- Sambal & Bumbu -->
            <button 
                @click="catalogSubtab = 'sambal-bumbu'" 
                :class="catalogSubtab === 'sambal-bumbu' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-red-500 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>
                <span>Sambal & Bumbu</span>
            </button>
            <!-- Herbal & Jamu -->
            <button 
                @click="catalogSubtab = 'herbal'" 
                :class="catalogSubtab === 'herbal' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M11 20A7 7 0 0 1 4 13C4 7 11 3 11 3s7 4 7 10a7 7 0 0 1-7 7Z"></path><path d="M11 3v17"></path></svg>
                <span>Herbal & Jamu</span>
            </button>
            <!-- Camilan Nusantara -->
            <button 
                @click="catalogSubtab = 'snack'" 
                :class="catalogSubtab === 'snack' ? 'bg-[#047857] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"></path><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"></path><path d="M2 7h20"></path></svg>
                <span>Camilan Nusantara</span>
            </button>
        </div>
    </div>

    <!-- 4. Authentic Indonesia Pantry Product Grid -->
    <div class="px-4">
        <div class="grid grid-cols-2 gap-3">
            <template x-for="(prod, idx) in getIndonesiaCatalogProducts()" :key="'indo-view-' + prod.id">
                <div class="bg-white border border-emerald-100 rounded-2xl p-2.5 shadow-xs flex flex-col justify-between hover:border-emerald-300 transition group">
                    <div>
                        <!-- Product Image + Origin & Halal Badge -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-zinc-50 cursor-pointer" @click="openProductDetail(prod)">
                            <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            
                            <!-- Asli Indonesia Badge -->
                            <div class="absolute top-2 left-2 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs border border-zinc-200 text-zinc-900 text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-red-600 inline-block"></span>
                                <span>Indonesia</span>
                            </div>

                            <!-- Halal Badge -->
                            <span class="absolute top-2 right-2 bg-[#047857] text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">
                                HALAL
                            </span>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 cursor-pointer hover:text-[#047857] leading-tight" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Netto / Berat -->
                        <div class="flex items-center gap-1 mt-1 text-[10px] text-zinc-500">
                            <svg class="w-3 h-3 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M12 3v18"></path><rect width="18" height="18" x="3" y="3" rx="2"></rect></svg>
                            <span x-text="'Netto: ' + (150 + ((idx * 50) % 350)) + 'g'"></span>
                        </div>

                        <!-- Price with JPY (~¥) Estimate -->
                        <div class="mt-1.5">
                            <span class="text-zinc-950 font-black text-sm tabular block leading-tight" x-text="formatRupiah(prod.final_price)"></span>
                            <div class="inline-flex items-center gap-1 mt-1 bg-emerald-50 text-[#047857] text-[10px] font-bold px-1.5 py-0.5 rounded tabular">
                                <span>Est.</span>
                                <span x-text="formatYen(prod.final_price)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3 pt-2 border-t border-zinc-100">
                        <template x-if="getCartItemQty(prod.id) === 0">
                            <button 
                                @click="addToCart(prod, 1)" 
                                class="w-full py-2 bg-[#047857] hover:bg-[#065F46] text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>+ Keranjang</span>
                            </button>
                        </template>
                        <template x-if="getCartItemQty(prod.id) > 0">
                            <div class="flex items-center justify-between w-full bg-emerald-50 border border-emerald-200 rounded-xl p-1">
                                <button @click="changeCartQty(prod.id, -1)" class="w-7 h-7 bg-white border border-emerald-200 rounded-lg text-emerald-700 font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-emerald-800 tabular" x-text="getCartItemQty(prod.id)"></span>
                                <button @click="changeCartQty(prod.id, 1)" class="w-7 h-7 bg-[#047857] rounded-lg text-white font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">+</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
