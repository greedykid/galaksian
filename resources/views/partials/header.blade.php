<!-- ================= TOP APP BAR (FIXED ROCK-SOLID) ================= -->
        <header class="fixed top-0 inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-white/95 backdrop-blur-md border-b border-zinc-200 shadow-2xs">
            <div class="px-4 py-3 flex items-center justify-between">
                <!-- Brand Identity -->
            <button @click="goToTab('home')" class="flex items-center gap-2.5 text-left group">
                <div class="w-7 h-7 rounded bg-zinc-950 flex items-center justify-center text-white font-bold text-xs tracking-wider transition group-hover:bg-[#E60012]">
                    G
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-[15px] tracking-tight text-zinc-950">GALAKSIAN</span>
                        <span class="text-[9px] font-bold tracking-wider uppercase px-1 py-0.2 rounded bg-zinc-100 text-zinc-700 border border-zinc-200">JP•ID</span>
                    </div>
                    <p class="text-[10px] text-zinc-500 font-medium" x-text="t('app_tagline', 'Jastip Indonesia — Jepang')">Jastip Indonesia — Jepang</p>
                </div>
            </button>

            <!-- Navigation Actions: Language Selector, WA CS, Notifications -->
            <div class="flex items-center gap-1.5">
                <!-- Global Language Selector Dropdown -->
                <div class="relative" @click.away="langDropdownOpen = false">
                    <button 
                        @click="langDropdownOpen = !langDropdownOpen" 
                        type="button"
                        class="h-8 px-2 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 active:scale-95 flex items-center gap-1.5 text-zinc-800 transition shadow-2xs select-none min-h-[32px]"
                        :title="currentLang === 'id' ? 'Ubah Bahasa Aplikasi' : 'Change App Language'"
                        aria-label="Language selector">
                        <!-- Flag: ID -->
                        <template x-if="currentLang === 'id'">
                            <span class="inline-flex items-center justify-center w-5 h-3.5 rounded-[2px] overflow-hidden border border-zinc-200/80 shadow-2xs shrink-0">
                                <svg class="w-full h-full object-cover" viewBox="0 0 640 480">
                                    <path fill="#e70011" d="M0 0h640v240H0z"/>
                                    <path fill="#ffffff" d="M0 240h640v240H0z"/>
                                </svg>
                            </span>
                        </template>
                        <!-- Flag: EN -->
                        <template x-if="currentLang === 'en'">
                            <span class="inline-flex items-center justify-center w-5 h-3.5 rounded-[2px] overflow-hidden border border-zinc-200/80 shadow-2xs shrink-0">
                                <svg class="w-full h-full object-cover" viewBox="0 0 640 480">
                                    <path fill="#012169" d="M0 0h640v480H0z"/>
                                    <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-179L0 64V0h75z"/>
                                    <path fill="#C8102E" d="m424 288 216 159v33h-44L366 317l58-29zM640 0v10L439 161l32 40L640 37V0zm-401 192L0 44V0h11l252 188-24 4zm-73 96L0 410v70h17l196-146-47-46z"/>
                                    <path fill="#FFF" d="M240 0h160v480H240zM0 160h640v160H0z"/>
                                    <path fill="#C8102E" d="M267 0h106v480H267zM0 187h640v106H0z"/>
                                </svg>
                            </span>
                        </template>
                        <span class="text-[11px] font-bold tracking-tight text-zinc-900" x-text="currentLang.toUpperCase()"></span>
                        <svg class="w-3 h-3 text-zinc-400 transition-transform duration-150" :class="langDropdownOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                        class="absolute right-0 mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-zinc-200 py-1.5 z-50 overflow-hidden"
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
                                <svg class="w-3.5 h-3.5 text-[#E60012]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
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
                                <svg class="w-3.5 h-3.5 text-[#E60012]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- WhatsApp Customer Service Button -->
                <a :href="waCsUrl" target="_blank" :title="t('contact_wa_cs_title', 'Hubungi WhatsApp CS')" class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-700 hover:text-emerald-700 hover:border-emerald-300 hover:bg-emerald-50 transition">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                </a>

                <!-- Notification Bell -->
                <button @click="showNotifications = !showNotifications" class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-700 hover:bg-zinc-50 transition relative">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-[#E60012] rounded-full"></span>
                </button>
            </div>
            </div>

            <!-- Secondary Sticky Nav: Detail Transaksi Header (Seamless ZERO gap integration) -->
            <div x-show="activeSubView === 'order-detail'" class="px-4 py-2 border-t border-zinc-200 flex items-center justify-between">
                <button @click="closeOrderDetail()" class="text-xs font-bold text-zinc-700 hover:text-zinc-950 flex items-center gap-1.5 py-1.5 px-3 rounded-lg bg-zinc-100 hover:bg-zinc-200 transition min-h-[40px]">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span x-text="t('back_btn', 'Kembali')">Kembali</span>
                </button>
                <div class="text-center">
                    <h2 class="text-sm font-extrabold text-zinc-950" x-text="t('order_detail_title', 'Detail Transaksi')">Detail Transaksi</h2>
                    <span class="text-[10px] font-mono text-zinc-400 block" x-text="selectedOrderDetail ? selectedOrderDetail.order_number : ''"></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button @click="openQrisPayView()" class="px-3 py-1.5 bg-[#1657FF] hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-xs min-h-[38px]" :title="t('open_qris_pay', 'Buka Pembayaran QRIS')">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span>QR Pay</span>
                    </button>
                    <button @click="if (selectedOrderId) openOrderDetail(selectedOrderId)" class="text-xs font-semibold text-zinc-600 hover:text-zinc-950 p-2 min-h-[38px] min-w-[36px] flex items-center justify-center" title="Refresh">
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
