<!-- ================= TOP APP BAR (FIXED ROCK-SOLID) ================= -->
        <header class="fixed top-0 inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-[#1657FF] text-white border-b border-[#1657FF] shadow-xs">
            <div class="px-4 py-2.5 flex items-center justify-between">
                <!-- Brand Identity -->
                <button @click="goToTab('home')" class="flex items-center gap-2 text-left group">
                    <div class="w-7 h-7 rounded-lg bg-[#00D06C] text-white flex items-center justify-center font-black text-xs tracking-wider transition shadow-xs group-hover:bg-[#00B85F]">
                        G
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-[15px] tracking-tight text-white">GALAKSIAN</span>
                        </div>
                        <!-- Adaptive Subtitle for Consistent Context -->
                        <template x-if="activeTab === 'home' && !activeSubView">
                            <div class="flex items-center gap-1 text-[11px] text-white/90 font-medium cursor-pointer">
                                <span>Kirim ke:</span>
                                <span class="font-bold text-white">Tokyo, Jepang</span>
                                <svg class="w-3 h-3 text-white/80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </template>
                        <template x-if="activeTab === 'cart' && !activeSubView">
                            <p class="text-[11px] text-white/90 font-medium" x-text="t('shopping_cart', 'Keranjang Belanja') + ' • ' + (cart.total_qty || 0) + ' item'"></p>
                        </template>
                        <template x-if="activeTab === 'transactions' && !activeSubView">
                            <p class="text-[11px] text-white/90 font-medium" x-text="t('transaction_history', 'Daftar Transaksi')"></p>
                        </template>
                        <template x-if="activeTab === 'profile' && !activeSubView">
                            <p class="text-[11px] text-white/90 font-medium" x-text="t('my_profile', 'Profil Akun')"></p>
                        </template>
                        <template x-if="activeSubView">
                            <p class="text-[11px] text-white/90 font-medium" x-text="t('app_tagline', 'Jastip Indonesia — Jepang')"></p>
                        </template>
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

                        <!-- Language Dropdown Menu (Only ID and EN) -->
                        <div 
                            x-show="langDropdownOpen" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-zinc-200 py-1.5 z-50 overflow-hidden text-zinc-900"
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

            <!-- Secondary Sticky Nav: Detail Transaksi Header (Seamless ZERO gap integration) -->
            <div x-show="activeSubView === 'order-detail'" class="px-4 py-2 border-t border-white/20 bg-[#1657FF] text-white flex items-center justify-between">
                <button @click="closeOrderDetail()" class="text-xs font-bold text-white flex items-center gap-1.5 py-1.5 px-3 rounded-lg bg-white/15 hover:bg-white/25 transition min-h-[38px]">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span x-text="t('back_btn', 'Kembali')">Kembali</span>
                </button>
                <div class="text-center">
                    <h2 class="text-sm font-extrabold text-white" x-text="t('order_detail_title', 'Detail Transaksi')">Detail Transaksi</h2>
                    <span class="text-[10px] font-mono text-white/80 block" x-text="selectedOrderDetail ? selectedOrderDetail.order_number : ''"></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button @click="openQrisPayView()" class="px-3 py-1.5 bg-[#00D06C] hover:bg-[#00B85F] text-white text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-xs min-h-[38px]" :title="t('open_qris_pay', 'Buka Pembayaran QRIS')">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>QR Pay</span>
                    </button>
                    <button @click="if (selectedOrderId) openOrderDetail(selectedOrderId)" class="text-xs font-semibold text-white/80 hover:text-white p-2 min-h-[38px] min-w-[36px] flex items-center justify-center" title="Refresh">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                    </button>
                </div>
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
