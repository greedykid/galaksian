<!-- ========================================================= -->
            <!-- SUB-VIEW: INSTRUKSI PEMBAYARAN & TESTING SIMULATION       -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'payment-instruction'" class="space-y-4">
                <div class="px-4 pt-3 pb-2 border-b border-zinc-200 flex items-center justify-between">
                    <h2 class="text-base font-extrabold text-zinc-950 tracking-tight" x-text="t('payment_instructions', 'Instruksi Pembayaran')">Instruksi Pembayaran</h2>
                    <button 
                        @click="openOrderDetail(paymentResult?.order?.id || selectedOrderId)" 
                        class="text-xs font-bold text-zinc-700 hover:text-zinc-950 flex items-center gap-1 py-1.5 px-3 rounded-lg bg-zinc-100 hover:bg-zinc-200 transition min-h-[36px]">
                        <span x-text="t('order_detail_title', 'Detail Transaksi')">Detail Transaksi</span>
                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>

                <div class="px-4 space-y-3" x-show="paymentResult">
                    <!-- ================= PAYMENT SUCCESS SCREEN (after paid) ================= -->
                    <div x-show="paymentResult?.isPaid" class="space-y-4" x-cloak>
                        <!-- Success Hero: Green Checkmark -->
                        <div class="flex flex-col items-center text-center pt-2">
                            <div class="w-16 h-16 rounded-full bg-[#00D06C] flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <svg class="w-8 h-8 text-white stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <h3 class="text-lg font-extrabold text-zinc-950 tracking-tight mt-4" x-text="t('payment_success_title', 'Pembayaran Berhasil')">Pembayaran Berhasil</h3>
                            <p class="text-xs text-zinc-500 text-center mt-1.5 leading-relaxed max-w-[280px]" x-text="t('payment_success_desc', 'Pesanan kamu sedang diproses. Estimasi tiba 7-14 hari kerja.')">Pesanan kamu sedang diproses. Estimasi tiba 7-14 hari kerja.</p>
                        </div>

                        <!-- Order Detail Card -->
                        <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 space-y-3 shadow-2xs">
                            <div class="text-center pb-3 border-b border-zinc-100">
                                <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold block" x-text="t('total_bill_label', 'Total Tagihan')">Total Tagihan</span>
                                <span class="text-2xl font-extrabold text-[#00D06C] tabular block mt-1" x-text="formatRupiah(paymentResult?.invoice?.amount || paymentResult?.order?.grand_total || 0)"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                    <span x-text="t('bill_method_label', 'Metode')">Metode</span>
                                </span>
                                <span class="font-bold text-zinc-900 capitalize" x-text="paymentResult?.invoice?.payment_method || paymentResult?.payment?.method || 'virtual_account'"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7h8M8 12h8M8 17h8"></path><path d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"></path></svg>
                                    <span x-text="t('order_no_label', 'No. Order')">No. Order</span>
                                </span>
                                <span class="font-mono font-bold text-zinc-900" x-text="'#' + (paymentResult?.order?.order_number || '')"></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2.5 pt-1 pb-4">
                            <button 
                                @click="goToTab('cart')" 
                                class="w-full py-3.5 bg-[#00D06C] hover:bg-[#00B85F] text-white font-bold text-sm rounded-xl transition shadow-md shadow-emerald-500/20 active:scale-[0.99] flex items-center justify-center gap-1.5 min-h-[44px]"
                                x-text="t('back_to_cart_btn', 'Kembali ke Keranjang')">
                                Kembali ke Keranjang
                            </button>
                            <button 
                                @click="openOrderDetail(paymentResult?.order?.id || selectedOrderId)" 
                                class="w-full py-3 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 font-semibold text-sm rounded-xl transition min-h-[44px]"
                                x-text="t('view_order_detail_btn', 'Lihat Detail Pesanan')">
                                Lihat Detail Pesanan
                            </button>
                        </div>
                    </div>

                    <!-- ================= UNPAID / INSTRUCTION SCREEN (not yet paid) ================= -->
                    <div x-show="!paymentResult?.isPaid" class="space-y-3" x-cloak>
                    <!-- Status Header Card -->
                    <div 
                        :class="paymentResult?.isPaid ? 'bg-emerald-700 text-white' : 'bg-zinc-900 text-white'"
                        class="rounded-xl p-4 text-center space-y-1 transition">
                        <span class="text-[10px] font-mono tracking-wider uppercase block text-zinc-400" x-text="paymentResult?.isPaid ? t('status_completed', 'Status: Selesai') : t('deadline_countdown', 'Batas Waktu: 23 Jam 59 Menit')"></span>
                        <h3 class="font-extrabold text-base" x-text="paymentResult?.isPaid ? t('status_paid_title', 'PEMBAYARAN LUNAS') : t('status_unpaid_title', 'MENUNGGU PEMBAYARAN')"></h3>
                        <p class="text-xs text-zinc-300" x-text="paymentResult?.isPaid ? t('paid_desc', 'Pesanan berhasil dikonfirmasi dan masuk ke proses pembelanjaan di Jepang.') : t('unpaid_desc', 'Selesaikan pembayaran sebelum batas waktu berakhir.')"></p>
                    </div>

                    <!-- Payment Invoicing Details -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center pb-2 border-b border-zinc-100">
                            <span class="text-zinc-500" x-text="t('invoice_number', 'Nomor Invoice')">Nomor Invoice</span>
                            <span class="font-mono font-bold text-zinc-900" x-text="paymentResult?.invoice?.invoice_number"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-zinc-100">
                            <span class="text-zinc-500" x-text="t('order_number', 'Nomor Order')">Nomor Order</span>
                            <span class="font-mono font-bold text-zinc-900" x-text="paymentResult?.order?.order_number"></span>
                        </div>
                        <div class="flex justify-between items-center pt-1 text-sm font-bold text-zinc-950">
                            <span x-text="t('total_bill', 'Total Tagihan')">Total Tagihan</span>
                            <span class="text-[#E60012] tabular text-base font-extrabold" x-text="formatRupiah(paymentResult?.invoice?.amount || 0)"></span>
                        </div>
                    </div>

                    <!-- Payment Reference / QR -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-4 text-center space-y-3">
                        <template x-if="paymentResult?.payment?.payment_reference">
                            <div class="space-y-1.5">
                                <span class="text-xs text-zinc-500 block" x-text="t('payment_ref_code', 'Kode / Nomor Referensi Bayar:')">Kode / Nomor Referensi Bayar:</span>
                                <div class="bg-zinc-100 p-2.5 rounded-lg font-mono text-sm font-bold text-zinc-900 select-all border border-zinc-200" x-text="paymentResult?.payment?.payment_reference"></div>
                                <button @click="copyToClipboard(paymentResult?.payment?.payment_reference)" class="text-xs font-semibold text-[#E60012] hover:underline" x-text="t('copy_ref_number', 'Salin Nomor Referensi')">
                                    Salin Nomor Referensi
                                </button>
                            </div>
                        </template>

                        <!-- Clean Vector QR Code Simulation -->
                        <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-200 inline-block mx-auto text-center">
                            <div class="w-36 h-36 bg-white border border-zinc-300 rounded p-2 flex flex-col justify-between mx-auto shadow-sm">
                                <div class="grid grid-cols-5 gap-1 w-full h-full p-1 bg-white">
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-200 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                    <div class="bg-zinc-900 rounded-sm"></div>
                                </div>
                            </div>
                            <span class="text-[10px] text-zinc-500 mt-2 block font-medium" x-text="t('scan_qr_banking', 'Pindai QR menggunakan aplikasi mobile banking')">Pindai QR menggunakan aplikasi mobile banking</span>
                        </div>
                    </div>

                    <!-- ================= TESTING SIMULATION PANEL ================= -->
                    <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3.5 space-y-2">
                        <div class="flex items-center justify-between text-zinc-900">
                            <span class="font-bold text-xs uppercase tracking-wider font-mono" x-text="t('sandbox_testing', 'Testing Sandbox')">Testing Sandbox</span>
                            <span class="text-[10px] text-zinc-500" x-text="t('auto_verification_mode', 'Mode Verifikasi Otomatis')">Mode Verifikasi Otomatis</span>
                        </div>
                        <p class="text-[11px] text-zinc-600 leading-relaxed" x-text="t('simulate_webhook_desc', 'Simulasikan notifikasi webhook gateway untuk menguji transisi status pesanan menjadi Lunas secara real-time.')">
                            Simulasikan notifikasi webhook gateway untuk menguji transisi status pesanan menjadi Lunas secara real-time.
                        </p>
                        <button 
                            @click="simulatePaymentWebhook(paymentResult?.invoice?.invoice_number)" 
                            :disabled="isSimulatingPayment || paymentResult?.isPaid"
                            class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg transition disabled:opacity-50">
                            <span x-show="!isSimulatingPayment && !paymentResult?.isPaid" x-text="t('simulate_paid_btn', 'Simulasikan Pembayaran Berhasil (Webhook Test)')">Simulasikan Pembayaran Berhasil (Webhook Test)</span>
                            <span x-show="isSimulatingPayment" x-text="t('sending_webhook', 'Mengirim Webhook...')">Mengirim Webhook...</span>
                            <span x-show="paymentResult?.isPaid" x-text="t('status_paid_already', 'Status: Sudah Terbayar (Lunas)')">Status: Sudah Terbayar (Lunas)</span>
                        </button>
                    </div>

                    <!-- Action Buttons: Clear, Prominent Actions for the Newly Created Order -->
                    <div class="space-y-2 pt-1 pb-4">
                        <button 
                            @click="openOrderDetail(paymentResult?.order?.id || selectedOrderId)" 
                            class="w-full py-3 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm min-h-[44px]">
                            <span x-text="t('view_transaction_detail', 'Lihat Detail Transaksi')">Lihat Detail Transaksi</span>
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                        <button 
                            @click="goToTab('transactions')" 
                            class="w-full py-2.5 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-50 font-semibold text-xs rounded-xl transition min-h-[40px] flex items-center justify-center gap-1" 
                            x-text="t('open_transactions_arrow', 'Buka Daftar Transaksi')">
                            Buka Daftar Transaksi
                        </button>
                    </div>
                    </div>
                </div>
            </div>
