<!-- ================= TOP APP BAR (FIXED ROCK-SOLID) ================= -->
        <header 
            x-show="activeTab === 'home' && !activeSubView"
            class="fixed top-0 inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-[#1657FF] text-white shadow-none border-b-0"
            x-cloak>
    <!-- ================= HOME TAB STICKY CONTAINER ================= -->
    <div class="relative overflow-hidden">
            <!-- STATE A: STANDARD NAVBAR + STICKY SEARCH BAR CAPSULE -->
            <div 
                x-show="!isPastKatalog || searchQuery.trim().length > 0 || stickySearchExpanded"
                x-transition:enter="transition-all ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.99]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition-all ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.99]">
                
                <!-- Navbar Top Row -->
                <div class="px-4 py-2.5 flex items-center justify-between">
                    <!-- Brand Identity -->
                    <button @click="goToTab('home'); window.scrollTo({top: 0, behavior: 'smooth'})" class="flex items-center gap-2 text-left group">
                        <div class="w-7 h-7 rounded-lg bg-[#00D06C] text-white flex items-center justify-center font-black text-xs tracking-wider transition shadow-xs group-hover:bg-[#00B85F]">
                            G
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-extrabold text-[15px] tracking-tight text-white">GALAKSIAN</span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] text-white/90 font-medium cursor-pointer">
                                <span x-text="t('ship_to', 'Kirim ke:')">Kirim ke:</span>
                                <span class="font-bold text-white" x-text="t('tokyo_japan', 'Tokyo, Jepang')">Tokyo, Jepang</span>
                                <svg class="w-3 h-3 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                    </button>

                    <!-- Navigation Actions: CS, Language Selector, Notifications -->
                    <div class="flex items-center gap-1.5">
                        <!-- Customer Service Headphone Button -->
                        <a :href="waCsUrl" target="_blank" :title="t('contact_wa_cs_title', 'Hubungi WhatsApp CS')" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-white border border-white/20 bg-white/10 hover:bg-white/20 transition">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                                <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                            </svg>
                        </a>

                        <!-- Global Language Selector Dropdown -->
                        <div class="relative" @click.away="langDropdownOpen = false">
                            <button 
                                @click="langDropdownOpen = !langDropdownOpen" 
                                type="button"
                                class="h-8 px-2 rounded-lg flex items-center gap-1 bg-white/10 hover:bg-white/20 border border-white/20 text-white transition select-none min-h-[32px]"
                                :title="currentLang === 'id' ? 'Ubah Bahasa Aplikasi' : 'Change App Language'"
                                aria-label="Language selector">
                                <span class="text-[11px] font-bold tracking-tight text-white" x-text="currentLang.toUpperCase()"></span>
                                <svg class="w-3 h-3 text-white/80 transition-transform duration-150" :class="langDropdownOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                        </div>

                        <!-- Notification Bell -->
                        <button @click="showNotifications = !showNotifications" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center transition relative text-white border border-white/20 bg-white/10 hover:bg-white/20">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1.5 w-4 h-4 bg-[#00D06C] text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-xs">3</span>
                        </button>
                    </div>
                </div>

                <!-- Sticky Search Bar Capsule Row -->
                <div class="px-4 pb-2.5 pt-0.5">
                    <div class="relative flex items-center bg-white rounded-full p-1 pl-3.5 shadow-sm">
                        <!-- Magnifying Glass Icon -->
                        <svg class="w-4 h-4 text-zinc-400 shrink-0 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input 
                            id="global-search-input"
                            type="text" 
                            x-model="searchQuery" 
                            @input.debounce.300ms="searchProducts()"
                            @keydown.enter="searchProducts()"
                            :placeholder="t('search_placeholder', 'Cari produk dari Jepang...')" 
                            class="w-full bg-transparent text-xs text-zinc-800 placeholder-zinc-400 focus:outline-none pr-1"
                        >
                        <!-- Clear Button (when text exists) -->
                        <button 
                            type="button"
                            x-show="searchQuery && searchQuery.length > 0"
                            @click="clearSearch()"
                            class="p-1 text-zinc-400 hover:text-zinc-600 rounded-full transition mr-1 shrink-0"
                            :title="t('clear_search', 'Hapus pencarian')">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <button 
                            @click="searchProducts()"
                            :disabled="isSearching"
                            class="px-4 py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white text-xs font-bold rounded-full transition shadow-xs shrink-0 active:scale-95 disabled:opacity-75 flex items-center gap-1.5">
                            <template x-if="isSearching">
                                <svg class="w-3 h-3 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <span x-text="t('search_btn', 'Cari')">Cari</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- STATE B: KATALOG PRODUK INDONESIA BAR (Matches media_1788725137374.png) -->
            <div 
                x-show="isPastKatalog && !searchQuery.trim().length && !stickySearchExpanded"
                x-transition:enter="transition-all ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition-all ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-[0.99]"
                class="px-4 pt-2 pb-2.5 select-none"
                x-cloak>
                
                <!-- Top Row: KATALOG label on left + Search trigger on right -->
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-black uppercase tracking-wider text-white" x-text="t('catalog_badge', 'KATALOG')">KATALOG</span>
                    <div class="flex items-center gap-1.5">
                        <button 
                            @click="stickySearchExpanded = true; focusSearch()" 
                            class="h-6 px-2.5 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 text-white text-[10px] font-bold flex items-center gap-1 transition"
                            :title="t('search_products', 'Cari Produk')">
                            <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span x-text="t('search_btn', 'Cari')">Cari</span>
                        </button>
                        <button 
                            @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                            class="w-6 h-6 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 text-white flex items-center justify-center transition"
                            :title="t('back_to_top', 'Ke Atas')">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Bottom Row: Category Pills (Exactly like media_1788725137374.png) -->
                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-0.5 text-xs">
                    <!-- 1. Semua -->
                    <button 
                        @click="catalogFilter = 'all'"
                        :class="catalogFilter === 'all' ? 'bg-white text-[#1657FF] font-bold shadow-xs' : 'border border-white/40 text-white hover:bg-white/10 font-medium'"
                        class="px-4 py-1 rounded-full transition shrink-0">
                        <span x-text="t('all', 'Semua')">Semua</span>
                    </button>

                    <!-- 2. Elektronik (Clean SVG Laptop) -->
                    <button 
                        @click="catalogFilter = 'elektronik'; openBrandCategoryView('kategori', 'Elektronik', 'elektronik')"
                        :class="catalogFilter === 'elektronik' ? 'bg-white text-[#1657FF] font-bold shadow-xs' : 'border border-white/40 text-white hover:bg-white/10 font-medium'"
                        class="px-3.5 py-1 rounded-full transition shrink-0 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" :class="catalogFilter === 'elektronik' ? 'text-[#1657FF]' : 'text-zinc-200'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="2" y1="20" x2="22" y2="20"></line>
                        </svg>
                        <span x-text="t('category_electronics', 'Elektronik')">Elektronik</span>
                    </button>

                    <!-- 3. Fashion (Clean SVG Dress) -->
                    <button 
                        @click="catalogFilter = 'fashion'; openBrandCategoryView('kategori', 'Fashion', 'fashion')"
                        :class="catalogFilter === 'fashion' ? 'bg-white text-[#1657FF] font-bold shadow-xs' : 'border border-white/40 text-white hover:bg-white/10 font-medium'"
                        class="px-3.5 py-1 rounded-full transition shrink-0 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" :class="catalogFilter === 'fashion' ? 'text-[#1657FF]' : 'text-rose-300'" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C10.9 2 10 2.9 10 4V5.1C7.8 5.7 6.1 7.4 5.4 9.6L4.1 13.5C3.9 14.1 4.3 14.7 5 14.7H6V21C6 21.6 6.4 22 7 22H17C17.6 22 18 21.6 18 21V14.7H19C19.7 14.7 20.1 14.1 19.9 13.5L18.6 9.6C17.9 7.4 16.2 5.7 14 5.1V4C14 2.9 13.1 2 12 2ZM12 4C12.6 4 13 4.4 13 5V5.1C12.7 5 12.3 5 12 5C11.7 5 11.3 5 11 5.1V4C11 4.4 11.4 4 12 4Z"/>
                        </svg>
                        <span x-text="t('category_fashion', 'Fashion')">Fashion</span>
                    </button>

                    <!-- 4. Kecantikan (Clean SVG Sparkle / Cosmetic) -->
                    <button 
                        @click="catalogFilter = 'kecantikan'; openBrandCategoryView('kategori', 'Kecantikan', 'skincare-beauty')"
                        :class="catalogFilter === 'kecantikan' ? 'bg-white text-[#1657FF] font-bold shadow-xs' : 'border border-white/40 text-white hover:bg-white/10 font-medium'"
                        class="px-3.5 py-1 rounded-full transition shrink-0 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" :class="catalogFilter === 'kecantikan' ? 'text-[#1657FF]' : 'text-amber-300'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
                        </svg>
                        <span x-text="t('category_beauty', 'Kecantikan')">Kecantikan</span>
                    </button>
                </div>

            </div>
        </div>


            <!-- Global Language Dropdown Menu (Only ID and EN) -->
            <div 
                x-show="langDropdownOpen" 
                @click.away="langDropdownOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                class="absolute right-12 top-11 w-48 bg-white rounded-xl shadow-xl border border-zinc-200 py-1.5 z-50 overflow-hidden text-zinc-900"
                style="display: none;">
                <div class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-zinc-400 border-b border-zinc-100 mb-1" x-text="t('choose_language', 'Pilih Bahasa')">
                    Pilih Bahasa
                </div>
                
                <!-- 1. Bahasa Indonesia -->
                <button 
                    @click="setLanguage('id')" 
                    type="button"
                    class="w-full px-3 py-2 text-left flex items-center justify-between hover:bg-zinc-50 transition min-h-[40px]"
                    :class="currentLang === 'id' ? 'bg-zinc-50/90 font-bold text-zinc-950' : 'text-zinc-600 font-medium'">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center w-5 h-3.5 rounded-[2px] overflow-hidden border border-zinc-200/80 shadow-2xs shrink-0">
                            <svg class="w-full h-full object-cover" viewBox="0 0 640 480">
                                <path fill="#e70011" d="M0 0h640v240H0z"/>
                                <path fill="#ffffff" d="M0 240h640v240H0z"/>
                            </svg>
                        </span>
                        <span class="text-xs">Bahasa Indonesia</span>
                    </div>
                    <template x-if="currentLang === 'id'">
                        <svg class="w-3.5 h-3.5 text-[#1657FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </template>
                </button>

                <!-- 2. English -->
                <button 
                    @click="setLanguage('en')" 
                    type="button"
                    class="w-full px-3 py-2 text-left flex items-center justify-between hover:bg-zinc-50 transition min-h-[40px]"
                    :class="currentLang === 'en' ? 'bg-zinc-50/90 font-bold text-zinc-950' : 'text-zinc-600 font-medium'">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center w-5 h-3.5 rounded-[2px] overflow-hidden border border-zinc-200/80 shadow-2xs shrink-0">
                            <svg class="w-full h-full object-cover" viewBox="0 0 640 480">
                                <path fill="#012169" d="M0 0h640v480H0z"/>
                                <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-179L0 64V0h75z"/>
                                <path fill="#C8102E" d="m424 288 216 159v33h-44L366 317l58-29zM640 0v10L439 161l32 40L640 37V0zm-401 192L0 44V0h11l252 188-24 4zm-73 96L0 410v70h17l196-146-47-46z"/>
                                <path fill="#FFF" d="M240 0h160v480H240zM0 160h640v160H0z"/>
                                <path fill="#C8102E" d="M267 0h106v480H267zM0 187h640v106H0z"/>
                            </svg>
                        </span>
                        <span class="text-xs">English</span>
                    </div>
                    <template x-if="currentLang === 'en'">
                        <svg class="w-3.5 h-3.5 text-[#1657FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </template>
                </button>
            </div>

            <!-- NOTIFICATION DRAWER (DROPDOWN OVERLAY) -->
            <div x-show="showNotifications" @click.away="showNotifications = false" class="bg-zinc-900 text-white px-4 py-3 border-t border-zinc-800 space-y-2 text-xs shadow-xl" x-cloak>
                <div class="flex justify-between items-center text-zinc-400">
                    <span class="font-semibold text-[11px] uppercase tracking-wider" x-text="t('system_notif', 'Pemberitahuan Sistem')">Pemberitahuan Sistem</span>
                    <button @click="showNotifications = false" class="text-zinc-400 hover:text-white text-xs" x-text="t('close', 'Tutup')">Tutup</button>
                </div>
                <div class="p-2.5 bg-zinc-800/80 rounded-lg border border-zinc-700/50 space-y-0.5">
                    <p class="font-semibold text-white" x-text="t('trip_active_title', 'Trip Tokyo September 2026 Aktif')">Trip Tokyo September 2026 Aktif</p>
                    <p class="text-[11px] text-zinc-400 leading-relaxed" x-text="t('trip_active_desc', 'Pemesanan PO dibuka hingga 7 hari sebelum jadwal keberangkatan. Bebas biaya handling untuk pengguna baru.')">Pemesanan PO dibuka hingga 7 hari sebelum jadwal keberangkatan. Bebas biaya handling untuk pengguna baru.</p>
                </div>
            </div>
        </header>
