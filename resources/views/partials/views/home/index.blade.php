<!-- ========================================================= -->
            <!-- VIEW 1: BERANDA (HOME)                                    -->
            <!-- ========================================================= -->
            <div x-show="activeTab === 'home' && !activeSubView" class="space-y-4">
                
                <!-- Search Bar -->
                <div class="px-4 pt-3">
                    <div class="relative flex items-center">
                        <svg class="w-4 h-4 text-zinc-400 absolute left-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            @input.debounce.300ms="searchProducts()"
                            :placeholder="t('search_placeholder', 'Cari camilan Tokyo, mie instan, skincare...')" 
                            class="w-full pl-9 pr-8 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:bg-white focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition"
                        >
                        <button x-show="searchQuery" @click="searchQuery = ''; searchProducts()" class="absolute right-3 text-zinc-400 hover:text-zinc-600">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Editorial Flight Trip Board -->
                <div class="px-4">
                    <div class="bg-zinc-950 text-white rounded-2xl p-4 border border-zinc-900 shadow-sm relative overflow-hidden">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-800 text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="font-mono uppercase text-zinc-300 font-bold" x-text="homeData.active_trip ? homeData.active_trip.code : 'REGULER BATCH'"></span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700">Open PO</span>
                        </div>

                        <!-- Route Visual -->
                        <div class="py-3 flex items-center justify-between text-center">
                            <div class="text-left">
                                <span class="text-[10px] text-zinc-400 uppercase tracking-wider block font-semibold" x-text="t('origin', 'Origin')">Origin</span>
                                <span class="text-lg font-black tracking-tight text-white">TOKYO</span>
                                <span class="text-[10px] text-zinc-500 block font-mono">HND / NRT</span>
                            </div>
                            <div class="flex-1 px-4 flex flex-col items-center">
                                <span class="text-[10px] font-mono text-zinc-400" x-text="t('direct_route', 'Direct Route')">Direct Route</span>
                                <div class="w-full flex items-center my-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-600"></div>
                                    <div class="flex-1 border-t border-dashed border-zinc-700 mx-1"></div>
                                    <svg class="w-3.5 h-3.5 text-[#E60012] rotate-90" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                                    </svg>
                                    <div class="flex-1 border-t border-dashed border-zinc-700 mx-1"></div>
                                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-600"></div>
                                </div>
                                <span class="text-[10px] text-emerald-400 font-medium" x-text="t('order_deadline', 'Batas Order: 7 Hari')">Batas Order: 7 Hari</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-zinc-400 uppercase tracking-wider block font-semibold" x-text="t('destination', 'Destinasi')">Destinasi</span>
                                <span class="text-lg font-black tracking-tight text-white">JAKARTA</span>
                                <span class="text-[10px] text-zinc-500 block font-mono">CGK</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-zinc-800/80 flex items-center justify-between text-[11px] text-zinc-400">
                            <span x-text="t('trip_notice', 'Bayar produk dulu, ongkir ditagih setelah barang ditimbang di Indonesia.')">Bayar produk dulu, ongkir ditagih setelah barang ditimbang di Indonesia.</span>
                        </div>
                    </div>
                </div>

                <!-- ================= 2. FLASH SALE SECTION ================= -->
                <div class="pt-1">
                    <div class="px-4 flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-sm text-zinc-950 tracking-tight" x-text="t('flash_sale', 'Flash Sale')">Flash Sale</span>
                            <!-- Sharp Monospace Countdown -->
                            <div class="flex items-center gap-0.5 bg-zinc-950 text-white font-mono text-[10px] font-bold px-1.5 py-0.5 rounded border border-zinc-900 tabular">
                                <span x-text="flashSaleCountdown.h">04</span>:
                                <span x-text="flashSaleCountdown.m">18</span>:
                                <span x-text="flashSaleCountdown.s">29</span>
                            </div>
                        </div>
                        <button @click="openBrandCategoryView('kategori', 'Flash Sale Promo', 'flash-sale')" class="text-xs font-semibold text-[#E60012] hover:underline" x-text="t('see_all', 'Lihat Semua')">
                            Lihat Semua
                        </button>
                    </div>

                    <!-- Horizontal Flash Sale Carousel -->
                    <div class="flex gap-2.5 overflow-x-auto px-4 no-scrollbar pb-1">
                        <template x-for="item in homeData.flash_sales" :key="item.id">
                            <div class="flex-shrink-0 w-36 bg-white border border-zinc-200 rounded-xl p-2 flex flex-col justify-between hover:border-zinc-300 transition">
                                <!-- Image with Tag -->
                                <div class="relative w-full h-28 bg-zinc-50 rounded-lg overflow-hidden cursor-pointer" @click="openProductDetail(item)">
                                    <img :src="item.primary_image || getFallbackImage(item)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                    <span class="absolute top-1 left-1 bg-[#E60012] text-white text-[9px] font-bold px-1.5 py-0.5 rounded font-mono" x-text="t('promo_badge', 'PROMO')">
                                        PROMO
                                    </span>
                                </div>
                                
                                <!-- Info -->
                                <div class="mt-2 flex-1">
                                    <h4 class="text-xs font-semibold text-zinc-900 line-clamp-1 cursor-pointer hover:text-[#E60012]" @click="openProductDetail(item)" x-text="item.name"></h4>
                                    <div class="mt-1">
                                        <span class="text-xs font-bold text-[#E60012] tabular" x-text="formatRupiah(item.final_price)"></span>
                                        <template x-if="item.has_discount">
                                            <span class="text-[10px] text-zinc-400 line-through block tabular" x-text="formatRupiah(item.price)"></span>
                                        </template>
                                    </div>
                                    <!-- Stock Progress Bar -->
                                    <div class="mt-1.5 w-full bg-zinc-100 rounded-full h-1 overflow-hidden">
                                        <div class="bg-[#E60012] h-full rounded-full" style="width: 70%"></div>
                                    </div>
                                    <span class="text-[9px] text-zinc-400 mt-0.5 block" x-text="(currentLang === 'en' ? 'Left ' : 'Sisa ') + item.stock + ' pcs'">Sisa <span class="font-semibold text-zinc-700" x-text="item.stock"></span> pcs</span>
                                </div>

                                <!-- Add to Cart Button or Stepper -->
                                <div class="mt-2 pt-2 border-t border-zinc-100">
                                    <template x-if="getCartItemQty(item.id) === 0">
                                        <button @click="addToCart(item, 1)" class="w-full py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition active:scale-[0.98]" x-text="t('add_to_cart_short', '+ Keranjang')">
                                            + Keranjang
                                        </button>
                                    </template>
                                    <template x-if="getCartItemQty(item.id) > 0">
                                        <div class="flex items-center justify-between w-full bg-zinc-50 border border-zinc-200 rounded-lg p-0.5">
                                            <button @click="changeCartQty(item.id, -1)" class="w-6 h-6 bg-white border border-zinc-200 rounded text-zinc-800 font-bold text-xs flex items-center justify-center">-</button>
                                            <span class="text-xs font-bold text-zinc-900 tabular" x-text="getCartItemQty(item.id)"></span>
                                            <button @click="changeCartQty(item.id, 1)" class="w-6 h-6 bg-[#E60012] rounded text-white font-bold text-xs flex items-center justify-center">+</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ================= 3. BRAND SECTION (2 Rows) ================= -->
                <div class="px-4 pt-1">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-extrabold text-sm text-zinc-950 tracking-tight">
                            Brand Populer
                        </h3>
                        <span class="text-[11px] text-zinc-400 font-medium">Jepang & Indonesia</span>
                    </div>
                    <!-- 2-Row Brand Grid -->
                    <div class="grid grid-rows-2 grid-flow-col gap-2 overflow-x-auto no-scrollbar pb-1">
                        <template x-for="brand in homeData.brands" :key="brand.id">
                            <button 
                                @click="openBrandCategoryView('brand', brand.name, brand.slug)" 
                                class="w-32 flex items-center gap-2.5 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl p-2 text-left cursor-pointer transition flex-shrink-0 group">
                                <div class="w-7 h-7 rounded-lg bg-white border border-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-900 group-hover:border-zinc-400">
                                    <span x-text="brand.name.substring(0,2).toUpperCase()"></span>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-semibold text-zinc-900 truncate" x-text="brand.name"></p>
                                    <span class="text-[10px] text-zinc-400" x-text="t('view_products', 'Lihat Produk')">Lihat Produk</span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- ================= 4. KATEGORI SECTION (2 Rows) ================= -->
                <div class="px-4 pt-1">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-extrabold text-sm text-zinc-950 tracking-tight" x-text="t('featured_categories', 'Kategori Pilihan')">
                            Kategori Pilihan
                        </h3>
                        <span class="text-[11px] text-zinc-400 font-medium">Katalog Lengkap</span>
                    </div>
                    <!-- 2-Row Category Grid -->
                    <div class="grid grid-rows-2 grid-flow-col gap-2 overflow-x-auto no-scrollbar pb-1">
                        <template x-for="cat in homeData.categories" :key="cat.id">
                            <button 
                                @click="openBrandCategoryView('kategori', cat.name, cat.slug)" 
                                class="w-36 flex items-center gap-2 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl p-2 text-left cursor-pointer transition flex-shrink-0 group">
                                <div class="w-7 h-7 rounded-lg bg-zinc-200/70 text-zinc-700 flex items-center justify-center text-xs group-hover:bg-zinc-900 group-hover:text-white transition">
                                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7.5 4.27 9 5.15"></path>
                                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                                        <path d="M12 22V12"></path>
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-semibold text-zinc-900 truncate" x-text="cat.name"></p>
                                    <span class="text-[10px] text-zinc-400" x-text="t('explore_category', 'Jelajahi')">Jelajahi</span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- ================= 5. CURATED TABS ================= -->
                <div class="px-4 pt-1">
                    <div class="flex gap-1.5 overflow-x-auto no-scrollbar border-b border-zinc-200 pb-2 text-xs font-semibold">
                        <button 
                            @click="curatedTab = 'special'" 
                            :class="curatedTab === 'special' ? 'bg-zinc-950 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'"
                            class="px-3 py-1.5 rounded-lg transition flex-shrink-0" x-text="t('curated_special', 'Untukmu')">
                            Untukmu
                        </button>
                        <button 
                            @click="curatedTab = 'belilagi'" 
                            :class="curatedTab === 'belilagi' ? 'bg-zinc-950 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'"
                            class="px-3 py-1.5 rounded-lg transition flex-shrink-0">
                            Beli Lagi
                        </button>
                        <button 
                            @click="curatedTab = 'bestseller'" 
                            :class="curatedTab === 'bestseller' ? 'bg-zinc-950 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'"
                            class="px-3 py-1.5 rounded-lg transition flex-shrink-0" x-text="t('curated_bestseller', 'Terlaris')">
                            Terlaris
                        </button>
                        <button 
                            @click="curatedTab = 'promo'" 
                            :class="curatedTab === 'promo' ? 'bg-zinc-950 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:text-zinc-900'"
                            class="px-3 py-1.5 rounded-lg transition flex-shrink-0" x-text="t('curated_discount', 'Diskon')">
                            Diskon
                        </button>
                    </div>
                </div>

                <!-- ================= 6. PRODUCT GRID (2 Columns) ================= -->
                <div id="catalog-grid" class="px-4 pt-1">
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="prod in getCuratedProducts()" :key="prod.id">
                            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden flex flex-col justify-between hover:border-zinc-300 transition">
                                <!-- Card Image & Origin -->
                                <div class="relative w-full aspect-square bg-zinc-50 cursor-pointer overflow-hidden" @click="openProductDetail(prod)">
                                    <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                    
                                    <!-- Country Pill -->
                                    <span class="absolute top-2 left-2 bg-white/95 text-zinc-800 text-[10px] font-bold px-1.5 py-0.5 rounded border border-zinc-200 shadow-sm">
                                        <span x-text="prod.origin_country === 'JP' ? 'JP' : 'ID'"></span>
                                    </span>

                                    <!-- Ready / PO Badge -->
                                    <span 
                                        :class="prod.availability_type === 'ready_stock' ? 'bg-emerald-700 text-white' : 'bg-zinc-900 text-white'"
                                        class="absolute top-2 right-2 text-[9px] font-bold px-1.5 py-0.5 rounded font-mono">
                                        <span x-text="prod.availability_type === 'ready_stock' ? 'READY' : 'PO'"></span>
                                    </span>
                                </div>

                                <!-- Card Details -->
                                <div class="p-2.5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <template x-if="prod.brand">
                                            <span class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wider block" x-text="prod.brand.name"></span>
                                        </template>
                                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-0.5 cursor-pointer hover:text-[#E60012]" @click="openProductDetail(prod)" x-text="prod.name"></h4>
                                        <div class="mt-1.5 flex items-baseline gap-1">
                                            <span class="text-xs font-bold text-zinc-950 tabular" x-text="formatRupiah(prod.final_price)"></span>
                                            <template x-if="prod.has_discount">
                                                <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(prod.price)"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Quick Add / Qty Controls -->
                                    <div class="mt-2.5 pt-2 border-t border-zinc-100">
                                        <template x-if="getCartItemQty(prod.id) === 0">
                                            <button @click="addToCart(prod, 1)" class="w-full py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition active:scale-[0.98]" x-text="t('add_to_cart_short', '+ Keranjang')">
                                                + Keranjang
                                            </button>
                                        </template>
                                        <template x-if="getCartItemQty(prod.id) > 0">
                                            <div class="flex items-center justify-between w-full bg-zinc-50 border border-zinc-200 rounded-lg p-0.5">
                                                <button @click="changeCartQty(prod.id, -1)" class="w-6 h-6 bg-white border border-zinc-200 rounded text-zinc-800 font-bold text-xs flex items-center justify-center">-</button>
                                                <span class="text-xs font-bold text-zinc-900 tabular" x-text="getCartItemQty(prod.id)"></span>
                                                <button @click="changeCartQty(prod.id, 1)" class="w-6 h-6 bg-[#E60012] rounded text-white font-bold text-xs flex items-center justify-center">+</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
