<!-- ========================================================= -->
<!-- VIEW 1: BERANDA (HOME)                                    -->
<!-- ========================================================= -->
<div x-show="activeTab === 'home' && !activeSubView" class="space-y-4 pb-8">
    
    <!-- ================= 1. ROYAL BLUE HERO SECTION ================= -->
    <div class="bg-[#1657FF] text-white px-4 pt-1 pb-3.5 shadow-sm -mx-px w-[calc(100%+2px)]">
        <!-- Tokyo Summer Hero Carousel (Auto-sliding & Swipeable) -->
        <div x-show="!searchQuery || !searchQuery.trim()">
            <div class="relative overflow-hidden min-h-[140px] select-none cursor-grab active:cursor-grabbing touch-pan-y" 
             @mouseenter="pauseHeroCarousel()" 
             @mouseleave="startHeroCarousel()"
             @touchstart="handleHeroTouchStart($event)"
             @touchmove="handleHeroTouchMove($event)"
             @touchend="handleHeroTouchEnd()"
             @mousedown="handleHeroMouseDown($event)">
            <div 
                class="flex select-none" 
                :style="getHeroTrackStyle()">
                
                <!-- Slide 0: Tokyo Summer -->
                <div class="w-full shrink-0 flex items-center justify-between min-h-[135px]">
                    <div class="max-w-[215px] z-10">
                        <span class="text-[10px] font-bold tracking-widest text-white/85 uppercase block">GALAKSIAN JASTIP</span>
                        <h2 class="text-xl font-black text-white leading-tight mt-0.5">Koleksi Tokyo<br>Summer</h2>
                        <p class="text-[11px] text-white/90 font-medium mt-1 leading-snug">Fashion terbaru langsung dari Tokyo</p>
                        <button 
                            @click="openBrandCategoryView('kategori', 'Tokyo Summer Collection', 'tokyo-summer')"
                            class="mt-3 px-4 py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white text-xs font-bold rounded-full shadow-md transition active:scale-95 inline-flex items-center gap-1">
                            Explore Koleksi
                        </button>
                    </div>

                    <!-- Right Graphic: Vector Shopping Bags -->
                    <div class="relative shrink-0 flex items-center justify-center w-28 h-28">
                        <div class="w-26 h-26 rounded-full bg-white/15 absolute"></div>
                        <svg class="w-22 h-22 relative z-10 drop-shadow-sm" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="22" y="32" width="38" height="46" rx="4" fill="#8B5CF6" stroke="#1E1B4B" stroke-width="3"/>
                            <path d="M33 32V24C33 19.5 37 16 41 16C45 16 49 19.5 49 24V32" stroke="#FBBF24" stroke-width="3.5" stroke-linecap="round"/>
                            <rect x="42" y="40" width="36" height="38" rx="4" fill="#06B6D4" stroke="#1E1B4B" stroke-width="3"/>
                            <path d="M52 40V33C52 29.5 55 26.5 60 26.5C65 26.5 68 29.5 68 33V40" stroke="#FBBF24" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M47 48H72" stroke="white" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 3"/>
                            <circle cx="60" cy="58" r="6" fill="#00D06C" stroke="#1E1B4B" stroke-width="2"/>
                            <path d="M57.5 58L59.5 60L62.5 56.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <!-- Slide 1: Kuliner & Snack Autentik -->
                <div class="w-full shrink-0 flex items-center justify-between min-h-[135px]">
                    <div class="max-w-[215px] z-10">
                        <span class="text-[10px] font-bold tracking-widest text-amber-300 uppercase block">FLASH SALE MINGGUAN</span>
                        <h2 class="text-xl font-black text-white leading-tight mt-0.5">Snack & Kuliner<br>Autentik Tokyo</h2>
                        <p class="text-[11px] text-white/90 font-medium mt-1 leading-snug">Camilan manis, ramen & bumbu impor</p>
                        <button 
                            @click="openBrandCategoryView('kategori', 'Snack & Cemilan', 'snack-cemilan')"
                            class="mt-3 px-4 py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white text-xs font-bold rounded-full shadow-md transition active:scale-95 inline-flex items-center gap-1">
                            Lihat Promo
                        </button>
                    </div>

                    <!-- Right Graphic: Bento Box / Japanese Treats Vector SVG -->
                    <div class="relative shrink-0 flex items-center justify-center w-28 h-28">
                        <div class="w-26 h-26 rounded-full bg-white/15 absolute"></div>
                        <svg class="w-22 h-22 relative z-10 drop-shadow-sm" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="20" y="34" width="60" height="42" rx="6" fill="#F97316" stroke="#1E1B4B" stroke-width="3"/>
                            <path d="M20 54H80" stroke="#1E1B4B" stroke-width="2.5"/>
                            <path d="M50 54V76" stroke="#1E1B4B" stroke-width="2.5"/>
                            <polygon points="35,40 25,50 45,50" fill="#FFFFFF" stroke="#1E1B4B" stroke-width="2"/>
                            <rect x="32" y="47" width="6" height="3" fill="#18181B"/>
                            <circle cx="65" cy="44" r="5" fill="#22C55E" stroke="#1E1B4B" stroke-width="2"/>
                            <circle cx="55" cy="46" r="4" fill="#EAB308" stroke="#1E1B4B" stroke-width="2"/>
                            <rect x="58" y="60" width="14" height="12" rx="3" fill="#FEF08A" stroke="#1E1B4B" stroke-width="2"/>
                        </svg>
                    </div>
                </div>

                <!-- Slide 2: Jastip Cepat Tokyo - Jakarta -->
                <div class="w-full shrink-0 flex items-center justify-between min-h-[135px]">
                    <div class="max-w-[215px] z-10">
                        <span class="text-[10px] font-bold tracking-widest text-emerald-300 uppercase block">TITIP APA SAJA</span>
                        <h2 class="text-xl font-black text-white leading-tight mt-0.5">Jastip Cepat<br>Tokyo - Jakarta</h2>
                        <p class="text-[11px] text-white/90 font-medium mt-1 leading-snug">Bebas titip barang impian dari Jepang</p>
                        <a 
                            :href="waCsUrl" target="_blank"
                            class="mt-3 px-4 py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white text-xs font-bold rounded-full shadow-md transition active:scale-95 inline-flex items-center gap-1">
                            Titip Sekarang
                        </a>
                    </div>

                    <!-- Right Graphic: Travel Luggage Vector SVG -->
                    <div class="relative shrink-0 flex items-center justify-center w-28 h-28">
                        <div class="w-26 h-26 rounded-full bg-white/15 absolute"></div>
                        <svg class="w-22 h-22 relative z-10 drop-shadow-sm" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="30" y="32" width="40" height="48" rx="5" fill="#38BDF8" stroke="#1E1B4B" stroke-width="3"/>
                            <circle cx="38" cy="82" r="3" fill="#18181B"/>
                            <circle cx="62" cy="82" r="3" fill="#18181B"/>
                            <path d="M42 32V20H58V32" stroke="#FBBF24" stroke-width="3" stroke-linecap="round"/>
                            <path d="M30 46H70M30 64H70" stroke="#1E1B4B" stroke-width="2.5"/>
                            <circle cx="50" cy="55" r="5" fill="#00D06C" stroke="#1E1B4B" stroke-width="2"/>
                            <path d="M48 55L50 57L53 53" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>

            </div>
        </div>

        <!-- Carousel Dots Indicator (Interactive & Animated) -->
        <div class="flex items-center justify-center gap-1.5 mt-2">
            <button 
                @click="setHeroSlide(0)" 
                :class="heroSlide === 0 ? 'w-4 h-1.5 bg-[#00D06C]' : 'w-1.5 h-1.5 bg-white/40 hover:bg-white/60'"
                class="rounded-full transition-all duration-300 focus:outline-none"
                aria-label="Slide 1">
            </button>
            <button 
                @click="setHeroSlide(1)" 
                :class="heroSlide === 1 ? 'w-4 h-1.5 bg-[#00D06C]' : 'w-1.5 h-1.5 bg-white/40 hover:bg-white/60'"
                class="rounded-full transition-all duration-300 focus:outline-none"
                aria-label="Slide 2">
            </button>
            <button 
                @click="setHeroSlide(2)" 
                :class="heroSlide === 2 ? 'w-4 h-1.5 bg-[#00D06C]' : 'w-1.5 h-1.5 bg-white/40 hover:bg-white/60'"
                class="rounded-full transition-all duration-300 focus:outline-none"
                aria-label="Slide 3">
            </button>
        </div>
        </div>
    </div>

    <!-- ================= SEARCH RESULTS VIEW (WHEN SEARCHING) ================= -->
    <div x-show="searchQuery && searchQuery.trim().length > 0" class="space-y-4 px-4 pt-1" x-cloak>
        <!-- Search Header Bar -->
        <div class="bg-white border border-zinc-200/90 rounded-2xl p-3 shadow-2xs flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xs font-bold text-zinc-900" x-text="t('search_results_title', 'Hasil Pencarian')">Hasil Pencarian</span>
                        <span class="text-xs font-extrabold text-[#1657FF] truncate max-w-[160px]" x-text="'“' + searchQuery.trim() + '”'"></span>
                    </div>
                    <p class="text-[10px] text-zinc-500 font-medium" x-text="isSearching ? t('search_searching', 'Mencari produk...') : (searchTotal + ' ' + t('search_found', 'produk ditemukan'))"></p>
                </div>
            </div>
            <button 
                @click="clearSearch()" 
                class="text-[11px] font-bold text-zinc-500 hover:text-zinc-800 bg-zinc-100 hover:bg-zinc-200 active:bg-zinc-300 px-3 py-1.5 rounded-xl transition flex items-center gap-1 shrink-0">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                <span x-text="t('search_reset', 'Reset')">Reset</span>
            </button>
        </div>

        <!-- Loading State -->
        <template x-if="isSearching">
            <div class="py-12 text-center bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-2xs">
                <div class="inline-block w-8 h-8 border-3 border-[#1657FF] border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-semibold text-zinc-600 mt-3" x-text="t('search_searching', 'Mencari produk...')">Mencari produk...</p>
            </div>
        </template>

        <!-- Empty State (No Products Found) -->
        <template x-if="!isSearching && searchResults.length === 0 && hasSearched">
            <div class="py-10 px-4 text-center bg-white border border-zinc-200/80 rounded-2xl shadow-2xs space-y-3">
                <div class="w-16 h-16 rounded-full bg-zinc-100 flex items-center justify-center mx-auto text-zinc-400">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-zinc-900" x-text="t('search_not_found', 'Produk Tidak Ditemukan')">Produk Tidak Ditemukan</h4>
                    <p class="text-xs text-zinc-500 mt-1 max-w-[280px] mx-auto leading-relaxed">
                        Tidak ada produk yang cocok dengan kata kunci <span class="font-bold text-zinc-800" x-text="'“' + searchQuery + '”'"></span>. Coba gunakan kata kunci umum seperti mie, matcha, skincare, atau camilan.
                    </p>
                </div>
                <div class="pt-2 flex flex-col gap-2 max-w-[220px] mx-auto">
                    <button 
                        @click="clearSearch()" 
                        class="w-full py-2 bg-[#1657FF] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95">
                        Lihat Semua Produk
                    </button>
                    <a 
                        :href="waCsUrl + '&text=' + encodeURIComponent('Halo Admin Galaksian, saya mencari produk ' + searchQuery + ' tapi belum ada di katalog.')" 
                        target="_blank" 
                        class="w-full py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                        <span>Titip via WhatsApp</span>
                    </a>
                </div>
            </div>
        </template>

        <!-- Search Results Grid (2 Columns) -->
        <template x-if="!isSearching && searchResults.length > 0">
            <div class="grid grid-cols-2 gap-2.5 pb-6">
                <template x-for="prod in searchResults" :key="'search-' + prod.id">
                    <div class="bg-white border border-zinc-200/80 rounded-2xl p-2.5 shadow-2xs flex flex-col justify-between hover:border-zinc-300 transition">
                        <div>
                            <!-- Product Image with Discount & Availability Badges -->
                            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-zinc-100 cursor-pointer" @click="openProductDetail(prod)">
                                <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                
                                <!-- Discount badge -->
                                <template x-if="prod.has_discount || (prod.discount_price && prod.discount_price < prod.price)">
                                    <span class="absolute top-2 left-2 bg-[#00D06C] text-white text-[9px] font-black px-1.5 py-0.2 rounded-full shadow-2xs">
                                        <span x-text="'-' + Math.round((1 - ((prod.unit_price || prod.discount_price || prod.final_price) / prod.price)) * 100) + '%'"></span>
                                    </span>
                                </template>
                                
                                <!-- Ready / PO badge -->
                                <span 
                                    :class="prod.availability_type === 'ready_stock' ? 'bg-emerald-700 text-white' : 'bg-zinc-900 text-white'"
                                    class="absolute top-2 right-2 text-[8px] font-black px-1.5 py-0.5 rounded font-mono uppercase tracking-wider" 
                                    x-text="prod.availability_type === 'ready_stock' ? 'READY' : 'PO'">
                                </span>
                            </div>

                            <!-- Product Title -->
                            <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 leading-tight cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                            <!-- Price Section -->
                            <div class="mt-1.5 flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-[#00A862] font-extrabold text-xs tabular" x-text="formatRupiah(prod.final_price || prod.price)"></span>
                                <template x-if="prod.has_discount || (prod.discount_price && prod.discount_price < prod.price)">
                                    <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(prod.price)"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Bottom row: Cart quantity action -->
                        <div class="mt-2.5 pt-2 border-t border-zinc-100">
                            <template x-if="getCartItemQty(prod.id) === 0">
                                <button 
                                    @click="addToCart(prod, 1)" 
                                    class="w-full py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 shadow-2xs active:scale-95">
                                    <svg class="w-3.5 h-3.5 stroke-current" viewBox="0 0 24 24" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    <span x-text="t('add_to_cart_short', '+ Keranjang')">+ Keranjang</span>
                                </button>
                            </template>
                            <template x-if="getCartItemQty(prod.id) > 0">
                                <div class="flex items-center justify-between w-full bg-zinc-50 border border-zinc-200 rounded-xl p-0.5">
                                    <button @click="changeCartQty(prod.id, -1)" class="w-6 h-6 bg-white border border-zinc-200 rounded-lg text-zinc-800 font-bold text-xs hover:bg-zinc-100 flex items-center justify-center">-</button>
                                    <span class="text-xs font-bold text-zinc-900 tabular px-1" x-text="getCartItemQty(prod.id)"></span>
                                    <button @click="changeCartQty(prod.id, 1)" class="w-6 h-6 bg-[#1657FF] hover:bg-blue-700 rounded-lg text-white font-bold text-xs transition flex items-center justify-center">+</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- ================= STANDARD HOME CONTENT (WHEN NOT SEARCHING) ================= -->
    <div x-show="!searchQuery || !searchQuery.trim()" class="space-y-4">

    <!-- ================= 2. KATEGORI (5 SOFT PASTEL CARDS, NO EMOJIS) ================= -->
    <div class="px-4">
        <div class="flex items-center justify-between mb-2.5">
            <h3 class="font-bold text-base text-zinc-900 tracking-tight" x-text="t('categories', 'Kategori')">
                Kategori
            </h3>
            <button @click="openBrandCategoryView('kategori', 'Semua Kategori', 'all')" class="text-xs font-semibold text-[#1657FF] hover:underline" x-text="t('see_all', 'Lihat Semua')">
                Lihat Semua
            </button>
        </div>

        <!-- 5 Pastel Category Cards Row -->
        <div class="grid grid-cols-5 gap-1.5">
            <!-- 1. Mie & Sembako -->
            <button 
                @click="openBrandCategoryView('kategori', 'Mie & Sembako', 'makanan-instan')"
                class="flex flex-col items-center text-center group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-[#FFF8EE] border border-[#FFE8CC] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                    <!-- Clean Vector Ramen/Noodle Bowl SVG (No Emoji) -->
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <path d="M6 15C6 22 10.5 25 16 25C21.5 25 26 22 26 15H6Z" fill="#FFF" stroke="#18181B" stroke-width="1.8" stroke-linejoin="round"/>
                        <rect x="11" y="25" width="10" height="2" rx="1" fill="#18181B"/>
                        <path d="M8 17H24" stroke="#DC2626" stroke-width="1.4" stroke-dasharray="2 2"/>
                        <path d="M8 15C8 12 11 12 11 9M13 15C13 12 16 12 16 9M18 15C18 12 21 12 21 9M23 15C23 12 24 12 24 9" stroke="#EAB308" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M5 10L27 6" stroke="#18181B" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M5 13L27 9" stroke="#18181B" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-700 leading-tight mt-1.5 group-hover:text-zinc-950">Mie & Sembako</span>
            </button>

            <!-- 2. Kopi & Teh -->
            <button 
                @click="openBrandCategoryView('kategori', 'Kopi & Teh', 'minuman')"
                class="flex flex-col items-center text-center group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-[#FFFDF5] border border-[#FEF3C7] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                    <!-- Clean Vector Coffee Cup SVG (No Emoji) -->
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <path d="M7 13H21C21 18.5 17.5 21 14 21C10.5 21 7 18.5 7 13Z" fill="#FFF" stroke="#18181B" stroke-width="1.8"/>
                        <path d="M21 15H23C24.5 15 25.5 16 25.5 17.5C25.5 19 24.5 20 23 20H20" stroke="#18181B" stroke-width="1.8"/>
                        <ellipse cx="14" cy="14" rx="5.5" ry="1.5" fill="#78350F"/>
                        <path d="M5 23C8 25 20 25 23 23" stroke="#18181B" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M12 9C12 7.5 13.5 7 13.5 5.5M16 9C16 7.5 17.5 7 17.5 5.5" stroke="#92400E" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-700 leading-tight mt-1.5 group-hover:text-zinc-950">Kopi & Teh</span>
            </button>

            <!-- 3. Sambal & Bumbu -->
            <button 
                @click="openBrandCategoryView('kategori', 'Sambal & Bumbu', 'bumbu-dapur')"
                class="flex flex-col items-center text-center group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-[#FFF1F2] border border-[#FFE4E6] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                    <!-- Clean Vector Chili Pepper SVG (No Emoji) -->
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <path d="M23 7C23 7 23 16 17 21C12.5 24.5 7 23 7 23C7 23 10 20 12 16C15 10 21 8 23 7Z" fill="#EF4444" stroke="#18181B" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M23 7C24.5 5.5 26.5 5 28 5" stroke="#16A34A" stroke-width="2" stroke-linecap="round"/>
                        <path d="M21 8C22 9.5 23.5 9 24 7.5" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-700 leading-tight mt-1.5 group-hover:text-zinc-950">Sambal & Bumbu</span>
            </button>

            <!-- 4. Herbal & Jamu -->
            <button 
                @click="openBrandCategoryView('kategori', 'Herbal & Jamu', 'bahan-masakan')"
                class="flex flex-col items-center text-center group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-[#F0FDF4] border border-[#DCFCE7] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                    <!-- Clean Vector Herbal Leaves SVG (No Emoji) -->
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <path d="M9 25C13 21 19 14 24 7" stroke="#18181B" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M24 7C20 7 17 10 17 13C21 13 24 10 24 7Z" fill="#4ADE80" stroke="#18181B" stroke-width="1.6"/>
                        <path d="M19 14C15 14 12 17 12 20C16 20 19 17 19 14Z" fill="#22C55E" stroke="#18181B" stroke-width="1.6"/>
                        <path d="M14 12C14 8 17 6 20 6C20 10 17 12 14 12Z" fill="#4ADE80" stroke="#18181B" stroke-width="1.6"/>
                        <path d="M10 18C10 15 13 13 16 13C16 17 13 18 10 18Z" fill="#22C55E" stroke="#18181B" stroke-width="1.6"/>
                    </svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-700 leading-tight mt-1.5 group-hover:text-zinc-950">Herbal & Jamu</span>
            </button>

            <!-- 5. Snack & Camilan -->
            <button 
                @click="openBrandCategoryView('kategori', 'Snack & Camilan', 'snack-cemilan')"
                class="flex flex-col items-center text-center group cursor-pointer">
                <div class="w-14 h-14 rounded-2xl bg-[#FFFBEB] border border-[#FEF3C7] flex items-center justify-center shadow-2xs group-hover:scale-105 transition">
                    <!-- Clean Vector Popcorn / Snack Bucket SVG (No Emoji) -->
                    <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                        <path d="M9 14L11 25C11 25.5 11.5 26 12 26H20C20.5 26 21 25.5 21 25L23 14H9Z" fill="#FEE2E2" stroke="#18181B" stroke-width="1.8"/>
                        <path d="M13 14L14 26M19 14L18 26" stroke="#EF4444" stroke-width="1.8"/>
                        <path d="M10 14C8.5 13 8.5 11 10 10C10 8.5 12 8 13.5 9C14.5 7.5 17 7.5 18 9C19.5 8 21.5 8.5 21.5 10C23 11 23 13 21.5 14H10Z" fill="#FEF08A" stroke="#18181B" stroke-width="1.6"/>
                    </svg>
                </div>
                <span class="text-[11px] font-medium text-zinc-700 leading-tight mt-1.5 group-hover:text-zinc-950">Snack & Camilan</span>
            </button>
        </div>
    </div>

    <!-- ================= 3. BUNDLE HEMAT DIASPORA (FLASH SALE) ================= -->
    <div class="pt-1">
        <!-- Header: Flame Pill + Countdown + Lihat > -->
        <div class="px-4 flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <!-- Blue Flame Pill (Clean SVG Flame, No Emoji) -->
                <div class="bg-[#1657FF] text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5 fill-current text-white" viewBox="0 0 24 24">
                        <path d="M12 23c-4.97 0-9-4.03-9-9 0-3.32 1.83-6.23 4.54-7.79.43-.25.99-.07 1.21.36.21.41.05.92-.35 1.15C6.11 9.04 5 11.4 5 14c0 3.86 3.14 7 7 7s7-3.14 7-7c0-2.31-.92-4.42-2.43-5.96-.34-.35-.33-.91.02-1.25.35-.34.91-.33 1.25.02C19.68 8.65 21 11.17 21 14c0 4.97-4.03 9-9 9zm0-7c-1.66 0-3-1.34-3-3 0-1.12.61-2.1 1.52-2.61.4-.23.91-.08 1.14.32.22.38.08.87-.29 1.08C10.74 12.15 10.5 12.6 10.5 13c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5c0-.62-.25-1.18-.66-1.59-.34-.34-.34-.9 0-1.24.34-.34.9-.34 1.24 0C14.73 10.82 15 11.87 15 13c0 1.66-1.34 3-3 3z"/>
                    </svg>
                    <span>Bundle Hemat Diaspora</span>
                </div>

                <!-- Blue Countdown Boxes -->
                <div class="flex items-center gap-1">
                    <span class="bg-[#1657FF] text-white font-mono text-xs font-bold px-1.5 py-0.5 rounded shadow-2xs tabular" x-text="flashSaleCountdown.h">02</span>
                    <span class="text-[#1657FF] font-bold text-xs">:</span>
                    <span class="bg-[#1657FF] text-white font-mono text-xs font-bold px-1.5 py-0.5 rounded shadow-2xs tabular" x-text="flashSaleCountdown.m">45</span>
                    <span class="text-[#1657FF] font-bold text-xs">:</span>
                    <span class="bg-[#1657FF] text-white font-mono text-xs font-bold px-1.5 py-0.5 rounded shadow-2xs tabular" x-text="flashSaleCountdown.s">08</span>
                </div>
            </div>

            <!-- "Lihat >" link -->
            <button @click="openFlashSaleView('all')" class="text-xs font-bold text-[#1657FF] hover:underline flex items-center gap-0.5 cursor-pointer">
                <span>Lihat</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Horizontal Flash Sale Carousel -->
        <div class="flex gap-3 overflow-x-auto px-4 no-scrollbar pb-2 pt-1">
            <template x-for="item in (homeData.flash_sales?.length ? homeData.flash_sales : (homeData.product_grid?.data?.slice(0, 5) || []))" :key="'fs-' + item.id">
                <div class="w-[155px] bg-white border border-zinc-200/80 rounded-2xl p-2.5 shadow-xs shrink-0 flex flex-col justify-between hover:border-zinc-300 transition">
                    <div>
                        <!-- Product Image with Discount Tag -->
                        <div class="relative w-full h-32 rounded-xl overflow-hidden bg-zinc-100 cursor-pointer" @click="openProductDetail(item)">
                            <img :src="item.primary_image || getFallbackImage(item)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            <span class="absolute top-2 left-2 bg-[#00D06C] text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs">
                                <span x-text="item.has_discount ? ('-' + Math.round((1 - item.final_price / item.price) * 100) + '%') : '-42%'">-42%</span>
                            </span>
                        </div>

                        <!-- Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 leading-tight cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(item)" x-text="item.name"></h4>

                        <!-- Price -->
                        <div class="mt-1">
                            <span class="text-[#00A862] font-extrabold text-sm tabular block" x-text="formatRupiah(item.final_price)"></span>
                            <template x-if="item.has_discount || item.price > item.final_price">
                                <span class="text-[10px] text-zinc-400 line-through tabular block" x-text="formatRupiah(item.price)"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom row: Sales count + Circular green '+' button -->
                    <div class="mt-2.5 pt-2 border-t border-zinc-100 flex items-center justify-between">
                        <span class="text-[10px] text-zinc-400 font-medium" x-text="(item.stock ? (item.stock * 4 + 18) : 234) + ' terjual'">234 terjual</span>
                        <button 
                            @click="addToCart(item, 1)" 
                            class="w-7 h-7 rounded-full bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center font-bold shadow-xs transition active:scale-95" 
                            title="Tambah ke Keranjang">
                            <svg class="w-3.5 h-3.5 stroke-current" viewBox="0 0 24 24" stroke-width="3" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================= 4. KATALOG PRODUK INDONESIA (CATEGORY FILTER PILLS) ================= -->
    <div id="katalog-produk-indonesia" class="pt-1 scroll-mt-24">
        <div class="px-4 flex items-center justify-between mb-2">
            <h3 class="font-bold text-base text-zinc-900 tracking-tight">
                Katalog Produk Indonesia
            </h3>
            <button @click="openIndonesiaCatalogView('all')" class="text-xs font-semibold text-[#1657FF] hover:underline flex items-center gap-0.5 cursor-pointer">
                <span>Lihat Semua</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Filter Pills with Clean SVG Icons (No Emojis) -->
        <div class="flex gap-2 overflow-x-auto px-4 no-scrollbar pb-1 text-xs">
            <!-- 1. Semua -->
            <button 
                @click="catalogFilter = 'all'"
                :class="catalogFilter === 'all' ? 'bg-[#1657FF] text-white font-bold shadow-sm' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 font-medium'"
                class="px-4 py-2 rounded-full transition shrink-0">
                Semua
            </button>

            <!-- 2. Elektronik (Clean SVG Laptop) -->
            <button 
                @click="catalogFilter = 'elektronik'; openBrandCategoryView('kategori', 'Elektronik', 'elektronik')"
                :class="catalogFilter === 'elektronik' ? 'bg-[#1657FF] text-white font-bold shadow-sm' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 font-medium'"
                class="px-3.5 py-2 rounded-full transition shrink-0 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-zinc-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="20" x2="22" y2="20"></line>
                </svg>
                <span>Elektronik</span>
            </button>

            <!-- 3. Fashion (Clean SVG Dress / Shirt) -->
            <button 
                @click="catalogFilter = 'fashion'; openBrandCategoryView('kategori', 'Fashion', 'fashion')"
                :class="catalogFilter === 'fashion' ? 'bg-[#1657FF] text-white font-bold shadow-sm' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 font-medium'"
                class="px-3.5 py-2 rounded-full transition shrink-0 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C10.9 2 10 2.9 10 4V5.1C7.8 5.7 6.1 7.4 5.4 9.6L4.1 13.5C3.9 14.1 4.3 14.7 5 14.7H6V21C6 21.6 6.4 22 7 22H17C17.6 22 18 21.6 18 21V14.7H19C19.7 14.7 20.1 14.1 19.9 13.5L18.6 9.6C17.9 7.4 16.2 5.7 14 5.1V4C14 2.9 13.1 2 12 2ZM12 4C12.6 4 13 4.4 13 5V5.1C12.7 5 12.3 5 12 5C11.7 5 11.3 5 11 5.1V4C11 4.4 11.4 4 12 4Z"/>
                </svg>
                <span>Fashion</span>
            </button>

            <!-- 4. Kecantikan (Clean SVG Sparkle / Cosmetic) -->
            <button 
                @click="catalogFilter = 'kecantikan'; openBrandCategoryView('kategori', 'Kecantikan', 'skincare-beauty')"
                :class="catalogFilter === 'kecantikan' ? 'bg-[#1657FF] text-white font-bold shadow-sm' : 'bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 font-medium'"
                class="px-3.5 py-2 rounded-full transition shrink-0 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                </svg>
                <span>Kecantikan</span>
            </button>
        </div>
    </div>

    <!-- ================= 5. SPESIAL UNTUK KAMU ================= -->
    <div class="pt-1">
        <div class="px-4 flex items-center justify-between mb-2">
            <h3 class="font-bold text-base text-zinc-900 tracking-tight" x-text="t('special_for_you_title', 'Spesial Untuk Kamu')">
                Spesial Untuk Kamu
            </h3>
            <button @click="openSpecialForYouView('all')" class="text-xs font-semibold text-[#1657FF] hover:underline flex items-center gap-0.5 cursor-pointer">
                <span>Lihat Semua</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Horizontal Cards with HOT / NEW badges & Star Rating -->
        <div class="flex gap-3 overflow-x-auto px-4 no-scrollbar pb-2 pt-1">
            <template x-for="(item, idx) in (homeData.special_for_you?.length ? homeData.special_for_you : (homeData.product_grid?.data?.slice(0, 6) || []))" :key="'sfy-' + item.id">
                <div class="w-[145px] bg-white border border-zinc-200/80 rounded-2xl p-2.5 shadow-xs shrink-0 flex flex-col justify-between hover:border-zinc-300 transition">
                    <div>
                        <!-- Product Image with HOT / NEW Badge -->
                        <div class="relative w-full h-32 rounded-xl overflow-hidden bg-zinc-100 cursor-pointer" @click="openProductDetail(item)">
                            <img :src="item.primary_image || getFallbackImage(item)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            <span 
                                :class="idx % 2 === 0 ? 'bg-[#FF6B00]' : 'bg-[#1657FF]'"
                                class="absolute top-2 left-2 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-2xs tracking-wider"
                                x-text="idx % 2 === 0 ? 'HOT' : 'NEW'">
                                HOT
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-1 mt-2 cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(item)" x-text="item.name"></h4>

                        <!-- Star Rating (Clean SVG Star, No Emoji) -->
                        <div class="flex items-center gap-1 mt-1 text-[10px] text-zinc-500 font-semibold">
                            <svg class="w-3.5 h-3.5 text-amber-400 fill-current" viewBox="0 0 24 24">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <span x-text="item.rating || '4.9'">4.9</span>
                        </div>
                    </div>

                    <!-- Bottom row: Bold Green Price + Circular Green '+' button -->
                    <div class="mt-2.5 pt-2 border-t border-zinc-100 flex items-center justify-between">
                        <span class="text-[#00A862] font-extrabold text-xs tabular" x-text="formatRupiah(item.final_price)"></span>
                        <button 
                            @click="addToCart(item, 1)" 
                            class="w-7 h-7 rounded-full bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center font-bold shadow-xs transition active:scale-95"
                            title="Tambah ke Keranjang">
                            <svg class="w-3.5 h-3.5 stroke-current" viewBox="0 0 24 24" stroke-width="3" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================= 6. BELI LAGI (DISTINCTIVE BOLD BLUE PRICE) ================= -->
    <div class="pt-1">
        <div class="px-4 flex items-center justify-between mb-2">
            <h3 class="font-bold text-base text-zinc-900 tracking-tight" x-text="t('buy_again_title', 'Beli Lagi')">
                Beli Lagi
            </h3>
            <button @click="openBuyAgainView('all')" class="text-xs font-semibold text-[#1657FF] hover:underline flex items-center gap-0.5 cursor-pointer">
                <span>Lihat Semua</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Horizontal Cards with Distinctive Bold Blue Price (#1657FF) -->
        <div class="flex gap-3 overflow-x-auto px-4 no-scrollbar pb-2 pt-1">
            <template x-for="item in (homeData.beli_lagi?.length ? homeData.beli_lagi : (homeData.best_sellers?.length ? homeData.best_sellers : (homeData.product_grid?.data?.slice(1, 7) || [])))" :key="'bl-' + item.id">
                <div class="w-[145px] bg-white border border-zinc-200/80 rounded-2xl p-2.5 shadow-xs shrink-0 flex flex-col justify-between hover:border-zinc-300 transition">
                    <div>
                        <!-- Product Image -->
                        <div class="w-full h-32 rounded-xl overflow-hidden bg-zinc-100 cursor-pointer" @click="openProductDetail(item)">
                            <img :src="item.primary_image || getFallbackImage(item)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                        </div>

                        <!-- Product Name -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-1 mt-2 cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(item)" x-text="item.name"></h4>
                    </div>

                    <!-- Bottom row: BOLD BLUE PRICE (#1657FF) + Circular Green '+' button -->
                    <div class="mt-2.5 pt-2 border-t border-zinc-100 flex items-center justify-between">
                        <span class="text-[#1657FF] font-extrabold text-xs tabular" x-text="formatRupiah(item.final_price)"></span>
                        <button 
                            @click="addToCart(item, 1)" 
                            class="w-7 h-7 rounded-full bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center font-bold shadow-xs transition active:scale-95"
                            title="Tambah ke Keranjang">
                            <svg class="w-3.5 h-3.5 stroke-current" viewBox="0 0 24 24" stroke-width="3" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================= 7. SEMUA PRODUK (3-COLUMN GRID) ================= -->
    <div class="pt-1">
        <div class="px-4 flex items-center justify-between mb-2">
            <h3 class="font-bold text-base text-zinc-900 tracking-tight" x-text="t('all_products_title', 'Semua Produk')">
                Semua Produk
            </h3>
            <span class="text-xs text-zinc-400 font-medium" x-text="(homeData.product_grid?.data?.length || 30) + ' produk'">
                30 produk
            </span>
        </div>

        <!-- 3-Column Compact Grid -->
        <div class="grid grid-cols-3 gap-2 px-4 pb-6">
            <template x-for="prod in (homeData.product_grid?.data?.length ? homeData.product_grid.data : (homeData.promo_products || []))" :key="'grid-' + prod.id">
                <div class="bg-white border border-zinc-200/80 rounded-2xl p-1.5 shadow-2xs flex flex-col justify-between hover:border-zinc-300 transition">
                    <div>
                        <!-- Product Image with Discount Badge -->
                        <div class="relative w-full h-24 rounded-xl overflow-hidden bg-zinc-100 cursor-pointer" @click="openProductDetail(prod)">
                            <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            <span class="absolute top-1.5 left-1.5 bg-[#00D06C] text-white text-[9px] font-bold px-1.5 py-0.2 rounded-full shadow-2xs">
                                <span x-text="prod.has_discount ? ('-' + Math.round((1 - prod.final_price / prod.price) * 100) + '%') : '-31%'">-31%</span>
                            </span>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-[11px] font-semibold text-zinc-900 line-clamp-2 mt-1 leading-snug cursor-pointer hover:text-[#1657FF]" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Price Section -->
                        <div class="mt-0.5">
                            <span class="text-[#00A862] font-extrabold text-xs tabular block" x-text="formatRupiah(prod.final_price)"></span>
                            <template x-if="prod.has_discount || prod.price > prod.final_price">
                                <span class="text-[9px] text-zinc-400 line-through tabular block" x-text="formatRupiah(prod.price)"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom row: Sales counter + Circular Green '+' button -->
                    <div class="mt-1.5 pt-1.5 border-t border-zinc-100 flex items-center justify-between">
                        <span class="text-[10px] text-zinc-400 font-normal" x-text="(prod.stock ? (prod.stock * 3 + 12) : 234) + 'x'">234x</span>
                        <button 
                            @click="addToCart(prod, 1)" 
                            class="w-6 h-6 rounded-full bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center text-xs font-bold shadow-2xs transition active:scale-95"
                            title="Tambah ke Keranjang">
                            <svg class="w-3 h-3 stroke-current" viewBox="0 0 24 24" stroke-width="3" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
    </div>

</div>
