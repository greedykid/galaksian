<!-- ========================================================= -->
            <!-- SUBVIEW: PEMBAYARAN QRIS (FIGMA EXECUTIVE GRADE)          -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'qris-payment'" class="pb-12 bg-zinc-50 min-h-[85vh]">
                <!-- Blue Top Header (Consistent Royal Blue #1657FF) -->
                <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
                    <div class="flex items-center gap-3">
                        <button @click="closeQrisPayView()" class="w-9 h-9 rounded-xl border border-white/30 bg-white/15 text-white flex items-center justify-center hover:bg-white/25 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('qris_title', 'Pembayaran QRIS')">Pembayaran QRIS</h1>
                    </div>
                    <span class="text-xs font-mono text-white/80 font-medium" x-text="selectedOrderDetail ? ('#' + selectedOrderDetail.order_number) : '#GLK-2024-087234'"></span>
                </div>

                <!-- Hero: Total Amount & Timer -->
                <div class="bg-[#1657FF] text-white px-4 pb-6 -mx-px w-[calc(100%+2px)]">
                    <div class="text-center pt-2 pb-1 space-y-1.5">
                        <span class="text-[11px] font-bold tracking-widest text-blue-100/90 uppercase block" x-text="t('total_payment', 'TOTAL PEMBAYARAN')">TOTAL PEMBAYARAN</span>
                        <div class="text-3xl font-extrabold text-white tracking-tight" x-text="formatRupiah(getQrisPayAmount())"></div>
                        
                        <div class="pt-1">
                            <div class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-white/15 backdrop-blur-xs rounded-full border border-white/20 text-xs font-semibold text-white">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><span x-text="t('pay_within', 'Bayar dalam')">Bayar dalam</span> <span x-text="qrisCountdownText">44:57</span></span>
                            </div>
                        </div>
                        <p class="text-[11px] text-blue-100/80 font-medium pt-0.5" x-text="t('scan_prompt', 'Segera scan sebelum waktu habis')">Segera scan sebelum waktu habis</p>
                    </div>
                </div>

                <!-- Floating White QR Card Overlap -->
                <div class="px-4 -mt-7 relative z-10">
                    <div class="bg-white rounded-3xl p-6 shadow-lg border border-zinc-100 text-center space-y-3">
                        <!-- QR Image Container -->
                        <div class="p-3 bg-white border border-zinc-100 rounded-2xl inline-block shadow-2xs">
                            <img src="/images/qris_code_only.png" alt="QRIS Code" class="w-56 h-56 mx-auto object-contain" style="image-rendering: -webkit-optimize-contrast;" />
                        </div>

                        <!-- Merchant Info -->
                        <div class="space-y-1 pt-1">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-5 h-5 rounded-full bg-[#1657FF] text-white flex items-center justify-center text-[10px] font-black">
                                    G
                                </div>
                                <span class="text-xs font-bold text-zinc-900">Merchant: GALAKSIAN JASTIP</span>
                            </div>
                            <p class="text-[11px] font-medium text-zinc-500" x-text="t('valid_all_qris', 'Berlaku untuk semua aplikasi QRIS')">Berlaku untuk semua aplikasi QRIS</p>
                        </div>
                    </div>
                </div>

                <!-- Warning Notice Box -->
                <div class="px-4 mt-3.5">
                    <div class="bg-[#FEFCE8] border border-[#FEF08A] rounded-2xl p-3.5 flex items-start gap-2.5">
                        <div class="text-[#CA8A04] shrink-0 mt-0.5">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <p class="text-xs text-[#854D0E] font-medium leading-relaxed" x-text="t('qris_warning_notice', 'Jangan tutup halaman ini sebelum pembayaran terkonfirmasi. QR Code berlaku 1x pakai.')">
                            Jangan tutup halaman ini sebelum pembayaran terkonfirmasi. QR Code berlaku 1x pakai.
                        </p>
                    </div>
                </div>

                <!-- 2-Column Action Buttons -->
                <div class="px-4 mt-3.5 grid grid-cols-2 gap-3">
                    <button 
                        @click="checkQrisPaymentStatus()" 
                        :disabled="isCheckingQris"
                        class="w-full py-3 px-3 bg-[#1657FF] hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 shadow-xs transition min-h-[46px]">
                        <svg class="w-4 h-4 stroke-current fill-none" :class="isCheckingQris ? 'animate-spin' : ''" viewBox="0 0 24 24" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                        <span x-text="isCheckingQris ? t('checking_status', 'Memeriksa...') : t('refresh_page', 'Perbarui Halaman')"></span>
                    </button>
                    <button 
                        @click="cancelQrisPayment()" 
                        class="w-full py-3 px-3 bg-[#FEE2E2]/60 hover:bg-[#FEE2E2] active:bg-red-200 border border-red-200/80 text-red-600 text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition min-h-[46px]">
                        <span x-text="t('cancel_order', 'Batalkan Pesanan')">Batalkan Pesanan</span>
                    </button>
                </div>

                <!-- Collapsible Accordion: Cara Bayar dengan QRIS -->
                <div class="px-4 mt-3.5">
                    <div class="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-2xs">
                        <button 
                            @click="showQrisInstructions = !showQrisInstructions" 
                            class="w-full px-4 py-3.5 flex items-center justify-between text-left hover:bg-zinc-50/80 transition min-h-[48px]">
                            <div class="flex items-center gap-2.5">
                                <div class="text-zinc-600">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                </div>
                                <span class="text-xs font-bold text-zinc-900" x-text="t('qris_how_to', 'Cara Bayar dengan QRIS')">Cara Bayar dengan QRIS</span>
                            </div>
                            <div class="text-zinc-400 transition-transform duration-200" :class="showQrisInstructions ? 'rotate-180' : ''">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </button>
                        
                        <div x-show="showQrisInstructions" class="px-4 pb-4 pt-1 border-t border-zinc-100 text-xs text-zinc-600 space-y-2.5 font-normal">
                            <div class="flex items-start gap-2 pt-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                                <p x-text="t('qris_step_1', 'Buka aplikasi perbankan (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, Dana, ShopeePay).')">Buka aplikasi perbankan (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, Dana, ShopeePay).</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                                <p x-html="t('qris_step_2')"></p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                                <p x-text="t('qris_step_3', 'Arahkan kamera ke QR Code di atas hingga terdeteksi secara otomatis.')">Arahkan kamera ke QR Code di atas hingga terdeteksi secara otomatis.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">4</span>
                                <p x-html="t('qris_step_4')"></p>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-zinc-100 text-zinc-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">5</span>
                                <p x-text="t('qris_step_5', 'Konfirmasi pembayaran dan masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.')">Konfirmasi pembayaran dan masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer CS Link -->
                <div class="px-4 my-6 text-center space-y-1.5 text-xs text-zinc-400">
                    <p x-text="t('need_help_qris', 'Butuh bantuan dengan pembayaran pesanan ini?')">Butuh bantuan dengan pembayaran pesanan ini?</p>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Galaksian%2C%20saya%20butuh%20bantuan%20pembayaran%20QRIS" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-700 hover:text-zinc-950 underline underline-offset-2">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none text-[#25D366]" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        <span x-text="t('contact_cs_wa', 'Hubungi CS via WhatsApp')">Hubungi CS via WhatsApp</span>
                    </a>
                </div>
            </div>
