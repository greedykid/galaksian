<!-- ========================================================= -->
<!-- SUBVIEW: DETAIL TRANSAKSI (MATCHING REFERENCE MOCKUP)    -->
<!-- REFERENCE: media_1788738443019.png                       -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'order-detail'" class="space-y-3 w-full max-w-full bg-[#FAFAF9] min-h-screen">
    <!-- 1. Sticky Top Header for Detail Transaksi (Matching Reference) -->
    <div class="sticky top-0 z-30 bg-white border-b border-zinc-200 px-4 py-3 shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center justify-between">
            <!-- Back Button (Rounded White Card) -->
            <button @click="closeOrderDetail()" class="w-9 h-9 rounded-xl border border-zinc-200 bg-white flex items-center justify-center text-zinc-900 hover:bg-zinc-50 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>

            <!-- Title & Subtitle -->
            <div class="text-left flex-1 pl-3 pr-2 min-w-0">
                <h2 class="text-sm font-bold text-zinc-950 truncate" x-text="t('order_detail_title', 'Detail Transaksi')">Detail Transaksi</h2>
                <div class="text-[11px] text-zinc-400 font-normal truncate mt-0.5" x-show="selectedOrderDetail">
                    <span class="font-mono" x-text="selectedOrderDetail.order_number"></span>
                    <span class="mx-1">·</span>
                    <span x-text="formatDateTime(selectedOrderDetail.timestamps?.created_at || selectedOrderDetail.created_at)"></span>
                </div>
            </div>

            <!-- Right: QR Pay Button & Refresh -->
            <div class="flex items-center gap-1.5 shrink-0">
                <button @click="openQrisPayView()" class="px-3 py-1.5 bg-[#1657FF] hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-xs min-h-[36px] active:scale-95" :title="t('open_qris_pay', 'Buka Pembayaran QRIS')">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
                    <span>QR Pay</span>
                </button>
                <button @click="if (selectedOrderId) openOrderDetail(selectedOrderId)" class="text-zinc-400 hover:text-zinc-700 p-1.5 rounded-lg hover:bg-zinc-100 transition flex items-center justify-center min-h-[36px] min-w-[32px]" title="Refresh">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
                </button>
            </div>
        </div>

        <!-- Invoice Horizontal Selector Tabs/Chips (Matching Reference) -->
        <template x-if="selectedOrderDetail && selectedOrderDetail.invoices && selectedOrderDetail.invoices.length > 0">
            <div class="flex items-center gap-2 pt-2.5 overflow-x-auto no-scrollbar">
                <!-- Product Invoice Pill -->
                <template x-for="inv in getProductInvoices(selectedOrderDetail)" :key="inv.id">
                    <button 
                        @click="scrollToInvoice('invoice-card-product-' + inv.id)" 
                        class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition shadow-xs cursor-pointer border"
                        :class="inv.status === 'paid' ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-zinc-800 border-zinc-200 hover:border-zinc-400'">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span x-text="inv.invoice_number"></span>
                        <span 
                            class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full"
                            :class="inv.status === 'paid' ? 'bg-[#00D06C] text-white' : 'bg-amber-100 text-amber-800'"
                            x-text="inv.status === 'paid' ? t('status_paid', 'Lunas') : t('status_unpaid', 'Menunggu')">
                        </span>
                    </button>
                </template>

                <!-- Additional Invoice Pill (e.g. + INV-002) -->
                <template x-for="inv in getAdditionalInvoices(selectedOrderDetail)" :key="inv.id">
                    <button 
                        @click="scrollToInvoice('invoice-card-additional-' + inv.id)" 
                        class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-zinc-200 hover:border-zinc-400 text-zinc-800 transition shadow-xs cursor-pointer">
                        <span class="text-zinc-500 font-bold">+</span>
                        <span class="font-bold" x-text="inv.invoice_number"></span>
                        <span 
                            class="text-[9px] font-extrabold px-1.5 py-0.2 rounded-full"
                            :class="inv.status === 'paid' ? 'bg-[#00D06C] text-white' : 'bg-[#FEF3C7] text-[#D97706]'"
                            x-text="inv.status === 'paid' ? t('status_paid', 'Lunas') : t('status_unpaid', 'Menunggu Pembayaran')">
                        </span>
                    </button>
                </template>

                <!-- Shipping Invoice Pill (INV-SHIPPING) -->
                <button 
                    @click="scrollToInvoice('invoice-card-shipping')" 
                    class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-dashed border-amber-300 text-zinc-800 transition shadow-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none text-amber-600" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon></svg>
                    <span class="font-bold">INV-SHIPPING</span>
                    <span 
                        class="text-[9px] font-bold px-1.5 py-0.2 rounded-full"
                        :class="getShippingInvoice(selectedOrderDetail)?.status === 'paid' ? 'bg-[#00D06C] text-white' : 'bg-amber-100 text-amber-800'"
                        x-text="getShippingInvoice(selectedOrderDetail)?.status === 'paid' ? t('status_paid', 'Lunas') : (getShippingInvoice(selectedOrderDetail) ? t('status_unpaid', 'Menunggu') : t('status_calculating', 'Menghitung...'))">
                    </span>
                </button>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <template x-if="orderDetailLoading">
        <div class="px-4 py-16 text-center space-y-3">
            <div class="w-7 h-7 border-2 border-zinc-900 border-t-transparent rounded-full animate-spin mx-auto"></div>
            <p class="text-xs font-medium text-zinc-500" x-text="t('loading_order_details', 'Memuat rincian transaksi...')">Memuat rincian transaksi...</p>
        </div>
    </template>

    <!-- Loaded Detail Content -->
    <template x-if="!orderDetailLoading && selectedOrderDetail">
        <div class="px-4 space-y-3 pb-36 w-full max-w-full overflow-x-hidden box-border">
            
            <!-- Card 1: Order Number & Status Header (Matching Reference) -->
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 flex items-center justify-between shadow-xs">
                <div>
                    <h3 class="font-mono font-bold text-sm text-zinc-950" x-text="selectedOrderDetail.order_number"></h3>
                    <span class="text-xs text-zinc-400 font-medium block mt-0.5" x-text="formatDateTime(selectedOrderDetail.timestamps?.created_at || selectedOrderDetail.created_at)"></span>
                </div>
                <span 
                    class="text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-2xs"
                    :class="getOrderStatusColor(selectedOrderDetail.status)"
                    x-text="getOrderStatusLabel(selectedOrderDetail.status)">
                </span>
            </div>

            <!-- Card 2: Red Alert Box: Out of Stock (OOS) Notification -->
            <template x-if="hasOosNotice(selectedOrderDetail)">
                <div class="bg-[#FFF1F2] border border-[#FECDD3] rounded-2xl p-3.5 flex items-start gap-3 shadow-xs">
                    <div class="p-1 text-amber-600 shrink-0 mt-0.5">
                        <svg class="w-5 h-5 fill-current text-amber-500" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-[#BE123C] leading-snug" x-text="t('oos_alert_title', 'Stok habis di luar negeri. Admin telah menghubungi Anda via WhatsApp.')">
                            Stok habis di luar negeri. Admin telah menghubungi Anda via WhatsApp.
                        </h4>
                        <p class="text-[11px] text-[#E11D48]/85 mt-0.5 leading-relaxed" x-text="t('oos_alert_desc', 'Item yang stoknya habis ditandai di bawah.')">
                            Item yang stoknya habis ditandai di bawah.
                        </p>
                    </div>
                </div>
            </template>

            <!-- Card 3: Diperlukan Pembayaran Selisih (Matching Reference Yellow Alert Box with Countdown) -->
            <template x-if="getPendingAdditionalInvoice(selectedOrderDetail)">
                <div class="bg-white border border-[#FDE68A] rounded-2xl overflow-hidden shadow-xs">
                    <!-- Top Banner with Stopwatch -->
                    <div class="bg-[#F59E0B] px-3.5 py-2.5 flex items-center gap-2 text-white font-bold text-xs">
                        <svg class="w-4 h-4 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span x-text="t('diff_payment_title', 'Diperlukan Pembayaran Selisih')">Diperlukan Pembayaran Selisih</span>
                    </div>

                    <!-- Body with Countdown & WA Chat -->
                    <div class="p-3.5 space-y-3">
                        <p class="text-xs text-zinc-700 leading-relaxed">
                            <span x-text="t('diff_payment_desc_prefix', 'Produk pengganti memiliki harga lebih tinggi. Bayar selisih ')"></span>
                            <b class="text-zinc-950 font-extrabold" x-text="formatRupiah(getPendingAdditionalInvoice(selectedOrderDetail).amount)"></b>
                            <span x-text="t('diff_payment_desc_suffix', ' untuk melanjutkan pesanan.')"></span>
                        </p>

                        <!-- Yellow Countdown Container -->
                        <div class="bg-[#FFFBEB] border border-[#FDE68A] rounded-xl p-3 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[9px] font-bold text-amber-800 tracking-wider block mb-1.5 uppercase" x-text="t('diff_countdown_label', 'BATAS WAKTU PEMBAYARAN SELISIH')">BATAS WAKTU PEMBAYARAN SELISIH</span>
                                <!-- 4 Orange Boxes -->
                                <div class="flex items-center gap-1.5">
                                    <div class="w-9 h-11 bg-[#F59E0B] text-white rounded-lg flex flex-col items-center justify-center shadow-xs">
                                        <span class="text-xs font-black leading-none" x-text="getDiffCountdown(getPendingAdditionalInvoice(selectedOrderDetail)).days">02</span>
                                        <span class="text-[8px] font-medium opacity-90 leading-none mt-0.5">hari</span>
                                    </div>
                                    <div class="w-9 h-11 bg-[#F59E0B] text-white rounded-lg flex flex-col items-center justify-center shadow-xs">
                                        <span class="text-xs font-black leading-none" x-text="getDiffCountdown(getPendingAdditionalInvoice(selectedOrderDetail)).hours">23</span>
                                        <span class="text-[8px] font-medium opacity-90 leading-none mt-0.5">jam</span>
                                    </div>
                                    <div class="w-9 h-11 bg-[#F59E0B] text-white rounded-lg flex flex-col items-center justify-center shadow-xs">
                                        <span class="text-xs font-black leading-none" x-text="getDiffCountdown(getPendingAdditionalInvoice(selectedOrderDetail)).minutes">59</span>
                                        <span class="text-[8px] font-medium opacity-90 leading-none mt-0.5">menit</span>
                                    </div>
                                    <div class="w-9 h-11 bg-[#F59E0B] text-white rounded-lg flex flex-col items-center justify-center shadow-xs">
                                        <span class="text-xs font-black leading-none" x-text="getDiffCountdown(getPendingAdditionalInvoice(selectedOrderDetail)).seconds">00</span>
                                        <span class="text-[8px] font-medium opacity-90 leading-none mt-0.5">detik</span>
                                    </div>
                                </div>
                            </div>
                            <div class="max-w-[110px] text-right">
                                <p class="text-[10px] text-amber-700 font-medium leading-snug" x-text="t('diff_countdown_note', 'Jika tidak dibayar, pesanan kembali ke invoice awal')">
                                    Jika tidak dibayar, pesanan kembali ke invoice awal
                                </p>
                            </div>
                        </div>

                        <!-- WhatsApp Button: Chat Admin Konfirmasi -->
                        <a 
                            :href="selectedOrderDetail.actions?.whatsapp_contact_url || ('https://wa.me/6281200000001?text=Halo%20Admin%2C%20konfirmasi%20ganti%20produk%20pesanan%20' + selectedOrderDetail.order_number)" 
                            target="_blank" 
                            class="w-full py-2.5 bg-[#00D06C] hover:bg-[#00B85F] active:bg-[#00A855] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition active:scale-[0.99] min-h-[42px]">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.082-2.146-.517-1.748-.724-2.883-2.493-2.971-2.609-.088-.116-.713-.949-.713-1.808s.45-1.282.61-1.455c.16-.174.348-.218.464-.218.116 0 .232.002.333.007.106.005.249-.041.389.296.145.348.493 1.204.536 1.292.043.087.072.19.014.305-.058.116-.087.188-.174.29-.087.101-.183.227-.261.305-.088.087-.18.181-.077.357.102.174.455.75 1.047 1.278.761.678 1.403.888 1.602.986.199.098.316.084.433-.051.117-.135.5-.584.633-.787.133-.203.267-.17.449-.102.183.069 1.157.546 1.355.645.199.1.332.149.38.232.049.083.049.48-.095.885z"/></svg>
                            <span x-text="t('chat_admin_confirm', 'Chat Admin Konfirmasi')">Chat Admin Konfirmasi</span>
                        </a>
                    </div>
                </div>
            </template>

            <!-- Card 4: Status Pesanan (Matching 4-Step Stepper) -->
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 space-y-3 shadow-xs">
                <div class="flex items-center justify-between pb-1">
                    <h4 class="text-xs font-bold text-zinc-950" x-text="t('order_status', 'Status Pesanan')">Status Pesanan</h4>
                </div>

                <div class="space-y-0 relative pl-1 pt-1">
                    <!-- Step 1: Pesanan Diproses -->
                    <div class="flex items-start gap-3 relative pb-5">
                        <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="is4StepPassed(selectedOrderDetail.status, 2) ? 'bg-[#1657FF]' : 'bg-zinc-200'"></div>
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 shrink-0"
                            :class="is4StepPassed(selectedOrderDetail.status, 1) ? 'bg-[#1657FF] text-white' : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h5 class="text-xs font-bold" :class="is4StepPassed(selectedOrderDetail.status, 1) ? 'text-[#1657FF]' : 'text-zinc-400'" x-text="t('stepper_processed', 'Pesanan Diproses')">Pesanan Diproses</h5>
                            <span class="text-[11px] text-zinc-400 mt-0.5 block" x-text="formatDateTime(selectedOrderDetail.timestamps?.created_at || selectedOrderDetail.created_at)"></span>
                        </div>
                    </div>

                    <!-- Step 2: Menunggu Pengiriman -->
                    <div class="flex items-start gap-3 relative pb-5">
                        <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="is4StepPassed(selectedOrderDetail.status, 3) ? 'bg-[#1657FF]' : 'bg-zinc-200'"></div>
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 shrink-0"
                            :class="is4StepPassed(selectedOrderDetail.status, 2) ? (is4StepActive(selectedOrderDetail.status, 2) ? 'bg-[#F59E0B] text-white ring-4 ring-amber-100' : 'bg-[#1657FF] text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                            <template x-if="is4StepPassed(selectedOrderDetail.status, 3)">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </template>
                            <template x-if="!is4StepPassed(selectedOrderDetail.status, 3)">
                                <div class="w-2 h-2 rounded-full bg-white"></div>
                            </template>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h5 class="text-xs font-bold" :class="is4StepPassed(selectedOrderDetail.status, 2) ? (is4StepActive(selectedOrderDetail.status, 2) ? 'text-[#D97706]' : 'text-zinc-900') : 'text-zinc-400'" x-text="t('stepper_awaiting_shipping', 'Menunggu Pengiriman')">Menunggu Pengiriman</h5>
                            <p class="text-[11px] text-zinc-400 mt-0.5" x-text="t('stepper_awaiting_shipping_sub', 'Penjual mengkonfirmasi ongkir')">Penjual mengkonfirmasi ongkir</p>
                        </div>
                    </div>

                    <!-- Step 3: Dalam Pengiriman -->
                    <div class="flex items-start gap-3 relative pb-5">
                        <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="is4StepPassed(selectedOrderDetail.status, 4) ? 'bg-[#1657FF]' : 'bg-zinc-200'"></div>
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 shrink-0"
                            :class="is4StepPassed(selectedOrderDetail.status, 3) ? (is4StepActive(selectedOrderDetail.status, 3) ? 'bg-[#1657FF] text-white ring-4 ring-blue-100' : 'bg-[#1657FF] text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                            <template x-if="is4StepPassed(selectedOrderDetail.status, 4)">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </template>
                            <template x-if="!is4StepPassed(selectedOrderDetail.status, 4)">
                                <span>3</span>
                            </template>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h5 class="text-xs font-bold" :class="is4StepPassed(selectedOrderDetail.status, 3) ? 'text-zinc-900' : 'text-zinc-400'" x-text="t('stepper_shipping', 'Dalam Pengiriman')">Dalam Pengiriman</h5>
                            <p class="text-[11px] text-zinc-400 mt-0.5" x-text="t('stepper_shipping_sub', 'Estimasi 7-14 hari kerja')">Estimasi 7-14 hari kerja</p>
                        </div>
                    </div>

                    <!-- Step 4: Pesanan Selesai -->
                    <div class="flex items-start gap-3 relative">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 shrink-0"
                            :class="is4StepPassed(selectedOrderDetail.status, 4) ? 'bg-[#00D06C] text-white shadow-xs' : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                            <template x-if="is4StepPassed(selectedOrderDetail.status, 4)">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </template>
                            <template x-if="!is4StepPassed(selectedOrderDetail.status, 4)">
                                <span>4</span>
                            </template>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h5 class="text-xs font-bold" :class="is4StepPassed(selectedOrderDetail.status, 4) ? 'text-[#00A862]' : 'text-zinc-400'" x-text="t('stepper_completed', 'Pesanan Selesai')">Pesanan Selesai</h5>
                            <p class="text-[11px] text-zinc-400 mt-0.5" x-text="t('stepper_completed_sub', 'Konfirmasi penerimaan barang')">Konfirmasi penerimaan barang</p>
                        </div>
                    </div>
                </div>

                <!-- Expandable Status History Audit Logs -->
                <template x-if="selectedOrderDetail.status_histories && selectedOrderDetail.status_histories.length > 0">
                    <details class="mt-3 pt-2.5 border-t border-zinc-100 group">
                        <summary class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider cursor-pointer flex items-center justify-between hover:text-zinc-600 transition">
                            <span x-text="t('transaction_history_notes', 'Catatan Riwayat Transaksi:')">Catatan Riwayat Transaksi:</span>
                            <svg class="w-3.5 h-3.5 stroke-current fill-none transition-transform group-open:rotate-180" viewBox="0 0 24 24" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </summary>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1 mt-2">
                            <template x-for="history in selectedOrderDetail.status_histories" :key="history.id">
                                <div class="flex items-start gap-2 text-xs bg-zinc-50 p-2.5 rounded-lg border border-zinc-100">
                                    <div class="w-1.5 h-1.5 rounded-full bg-zinc-950 mt-1.5 shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="text-zinc-900 font-medium text-[11px]" x-text="history.note"></p>
                                        <span class="text-[10px] text-zinc-400 tabular" x-text="formatDateTime(history.created_at)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </details>
                </template>
            </div>

            <!-- Card 5: Alamat Pengiriman (Matching Reference with Blue House Icon) -->
            <template x-if="selectedOrderDetail.address">
                <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 space-y-3 shadow-xs">
                    <h4 class="text-xs font-bold text-zinc-950" x-text="t('shipping_address_label', 'Alamat Pengiriman')">Alamat Pengiriman</h4>
                    <div class="bg-[#F0F5FF] rounded-xl p-3.5 flex items-start gap-3 border border-blue-100/50">
                        <div class="w-9 h-9 rounded-xl bg-[#1657FF] text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                        </div>
                        <div class="text-xs flex-1 min-w-0">
                            <h5 class="font-bold text-zinc-950" x-text="selectedOrderDetail.address.recipient_name"></h5>
                            <p class="text-zinc-600 mt-0.5 leading-relaxed" x-text="selectedOrderDetail.address.address"></p>
                            <a :href="'tel:' + selectedOrderDetail.address.phone" class="text-xs font-bold text-[#1657FF] hover:underline mt-1 inline-block" x-text="selectedOrderDetail.address.phone"></a>
                            <template x-if="selectedOrderDetail.address.delivery_note">
                                <p class="text-[11px] text-zinc-500 bg-white/70 p-2 rounded-lg mt-1.5 border border-blue-200/40" x-text="(currentLang === 'en' ? 'Delivery Note: ' : 'Catatan Pengantaran: ') + getDeliveryNoteLabel(selectedOrderDetail.address.delivery_note)"></p>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Card 6: Daftar Produk (Matching Reference) -->
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 space-y-3 shadow-xs">
                <div class="flex items-center justify-between pb-1">
                    <h4 class="text-xs font-bold text-zinc-950" x-text="t('ordered_products', 'Daftar Produk')">Daftar Produk</h4>
                    <span class="text-xs text-zinc-400 font-medium" x-text="(selectedOrderDetail.items?.reduce((acc, it) => acc + (it.qty || 1), 0) || selectedOrderDetail.items?.length || 0) + ' Qty'"></span>
                </div>

                <div class="divide-y divide-zinc-100">
                    <template x-for="item in selectedOrderDetail.items" :key="item.id">
                        <div class="py-3.5 space-y-2.5">
                            <div class="flex items-start gap-3">
                                <!-- Thumbnail with fallback -->
                                <div class="relative shrink-0">
                                    <img 
                                        :src="item.image || getFallbackImage({ name: (item.clean_name || item.product_name || item.name) })" 
                                        x-on:error="$event.target.onerror = null; $event.target.src = getFallbackImage({ name: (item.clean_name || item.product_name || item.name) })"
                                        class="w-14 h-14 rounded-xl object-cover bg-zinc-100 border border-zinc-200/80" 
                                        :class="item.is_oos && !item.resolution && !item.refund_status && !item.is_replacement ? 'opacity-40 grayscale' : ''"
                                        alt="Item">
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <!-- Replacement Item Style -->
                                    <template x-if="item.is_replacement || (item.product_name && item.product_name.includes('(Pengganti'))">
                                        <div class="space-y-1">
                                            <h5 class="text-xs font-bold text-zinc-950 leading-snug" x-text="item.clean_name || item.product_name"></h5>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#EFF6FF] text-[#1D4ED8] border border-[#BFDBFE]" x-text="t('replacement_product_badge', 'Produk Pengganti')">Produk Pengganti</span>
                                            </div>
                                            <p class="text-[11px] text-zinc-400 italic" x-text="t('replaces_label', 'Menggantikan: ') + (item.replaced_product_name || 'Item Awal')"></p>
                                            
                                            <div class="flex items-center gap-2 flex-wrap pt-0.5">
                                                <span class="text-xs font-bold text-zinc-950" x-text="formatRupiah(item.price || item.unit_price)"></span>
                                                <template x-if="item.original_price && item.original_price !== item.unit_price">
                                                    <s class="text-[11px] text-zinc-400" x-text="formatRupiah(item.original_price)"></s>
                                                </template>
                                                <span class="text-[11px] text-zinc-500" x-text="'× ' + item.qty"></span>
                                                <template x-if="item.original_price && item.unit_price > item.original_price">
                                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#D97706]" x-text="'+' + formatRupiah((item.unit_price - item.original_price) * item.qty) + ' ' + t('diff_badge', 'selisih')"></span>
                                                </template>
                                                <template x-if="item.refund_amount && item.refund_amount > 0">
                                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800" x-text="'Refund ' + formatRupiah(item.refund_amount)"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Out of Stock Item Style -->
                                    <template x-if="item.is_oos && !item.resolution && !item.refund_status && !item.is_replacement && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                        <div class="space-y-1">
                                            <h5 class="text-xs font-semibold text-zinc-400 leading-snug line-through" x-text="item.product_name || item.name"></h5>
                                            <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full bg-[#FEE2E2] text-[#DC2626]" x-text="t('oos_stock_badge', 'Stok Kosong')">Stok Kosong</span>
                                            <p class="text-[11px] text-zinc-400 tabular" x-text="formatRupiah(item.price || item.unit_price) + ' × ' + item.qty"></p>
                                        </div>
                                    </template>

                                    <!-- Normal Item Style -->
                                    <template x-if="!item.is_oos && !item.is_replacement && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                        <div class="space-y-1">
                                            <h5 class="text-xs font-bold text-zinc-950 leading-snug" x-text="item.product_name || item.name"></h5>
                                            <p class="text-[11px] text-zinc-500 tabular" x-text="formatRupiah(item.price || item.unit_price) + ' × ' + item.qty"></p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Right Subtotal -->
                                <span class="font-bold text-xs shrink-0 tabular" 
                                    :class="item.is_oos && !item.resolution && !item.refund_status && !item.is_replacement ? 'text-zinc-400' : 'text-zinc-950'"
                                    x-text="formatRupiah(item.subtotal)">
                                </span>
                            </div>

                            <!-- Simulation & Resolution Action Controls -->
                            <div class="flex items-center justify-between text-xs pt-1">
                                <template x-if="!item.is_oos && !item.resolution && !item.refund_status && !item.is_replacement && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                    <button @click="triggerOosSimulation(item)" class="text-[10px] font-medium text-red-600 hover:text-red-800 flex items-center gap-1 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-md border border-red-200 transition">
                                        <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        <span x-text="t('sim_mark_oos', '[Simulasi] Tandai Barang Habis di JP')">[Simulasi] Tandai Barang Habis di JP</span>
                                    </button>
                                </template>
                                <template x-if="item.is_oos && !item.resolution && !item.refund_status && !item.is_replacement && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                    <button @click="openOosModal(item)" class="text-[10px] font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition flex items-center gap-1 shadow-xs min-h-[32px]">
                                        <span x-text="t('select_solution_btn', 'Pilih Solusi: Refund / Ganti Produk ➔')">Pilih Solusi: Refund / Ganti Produk ➔</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Card 6b: Klaim Refund Selisih via WhatsApp (Jika ada barang refund / selisih lebih murah) -->
            <template x-if="hasRefundItems()">
                <div class="bg-emerald-50/90 border border-emerald-200 rounded-2xl p-4 space-y-3 shadow-xs">
                    <div class="flex items-start gap-2.5">
                        <div class="p-2 bg-emerald-100 text-emerald-800 rounded-lg shrink-0 mt-0.5">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-emerald-950" x-text="t('claim_refund_diff_title', 'Klaim Pengembalian Dana (Refund Selisih)')">Klaim Pengembalian Dana (Refund Selisih)</h4>
                            <p class="text-[11px] text-emerald-800 leading-relaxed mt-0.5" x-text="(currentLang === 'en' ? 'There is a refund difference for your order amounting to ' : 'Terdapat pengembalian dana selisih harga dari pesanan Anda sebesar ') + formatRupiah(getTotalRefundAmount()) + (currentLang === 'en' ? '. Please contact our admin via WhatsApp to confirm your bank/e-wallet account for payout.' : '. Silakan hubungi admin melalui WhatsApp untuk konfirmasi nomor rekening/e-wallet pencairan dana Anda.')"></p>
                        </div>
                    </div>
                    <a 
                        :href="getWaRefundUrl(null)" 
                        target="_blank" 
                        class="w-full py-2.5 px-3 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition min-h-[42px]">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        <span x-text="t('chat_wa_request_refund_diff', 'Chat ke WA untuk Meminta Refund Selisih') + ' (' + formatRupiah(getTotalRefundAmount()) + ')'"></span>
                    </a>
                </div>
            </template>

            <!-- Card 7: Rincian Invoice (Matching Reference Nested Layout) -->
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 space-y-3.5 shadow-xs">
                <h4 class="text-xs font-bold text-zinc-950" x-text="t('invoices_title', 'Rincian Invoice')">Rincian Invoice</h4>

                <div class="space-y-3">
                    <!-- 1. INV-001 (Invoice Produk Original) -->
                    <template x-for="inv in getProductInvoices(selectedOrderDetail)" :key="inv.id">
                        <div :id="'invoice-card-product-' + inv.id" class="p-3.5 rounded-xl border border-zinc-200 bg-white space-y-2.5">
                            <div class="flex justify-between items-start">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-zinc-500 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    <div>
                                        <h5 class="font-mono font-bold text-xs text-zinc-950" x-text="inv.invoice_number">INV-001</h5>
                                        <span class="text-[10px] text-zinc-400 block" x-text="t('invoice_product_orig', 'Invoice Produk (Original)')">Invoice Produk (Original)</span>
                                    </div>
                                </div>
                                <span 
                                    class="text-[9px] font-bold px-2.5 py-0.5 rounded-full"
                                    :class="inv.status === 'paid' ? 'bg-[#E8F8F0] text-[#00A862]' : 'bg-[#FEF3C7] text-[#D97706]'"
                                    x-text="inv.status === 'paid' ? t('status_paid', 'Lunas') : t('status_unpaid', 'Menunggu Pembayaran')">
                                </span>
                            </div>

                            <!-- Breakdown Rows -->
                            <div class="space-y-1.5 text-xs pt-1 border-t border-zinc-100">
                                <div class="flex justify-between items-center text-zinc-600">
                                    <span x-text="t('total_goods_price', 'Total Harga Barang')">Total Harga Barang</span>
                                    <span class="font-medium text-zinc-950 tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.product_subtotal || inv.amount)"></span>
                                </div>
                                <template x-if="selectedOrderDetail.pricing?.handling_fee_amount > 0">
                                    <div class="flex justify-between items-center text-zinc-600">
                                        <span x-text="t('total_service_fee', 'Biaya Layanan')">Biaya Layanan</span>
                                        <span class="font-medium text-zinc-950 tabular" x-text="formatRupiah(selectedOrderDetail.pricing.handling_fee_amount)"></span>
                                    </div>
                                </template>
                                <template x-if="selectedOrderDetail.pricing?.insurance_amount > 0">
                                    <div class="flex justify-between items-center text-zinc-600">
                                        <span>Asuransi</span>
                                        <span class="font-medium text-zinc-950 tabular" x-text="formatRupiah(selectedOrderDetail.pricing.insurance_amount)"></span>
                                    </div>
                                </template>
                                <template x-if="selectedOrderDetail.pricing?.voucher_amount > 0">
                                    <div class="flex justify-between items-center text-emerald-600">
                                        <span x-text="t('voucher_discount_label', 'Diskon Voucher:')">Diskon Voucher:</span>
                                        <span class="font-medium tabular" x-text="'- ' + formatRupiah(selectedOrderDetail.pricing.voucher_amount)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center font-bold text-xs pt-1 border-t border-zinc-100">
                                    <span class="text-zinc-800" x-text="'Subtotal ' + inv.invoice_number">Subtotal INV-001</span>
                                    <span class="text-[#00A862] font-black tabular text-sm" x-text="formatRupiah(inv.amount)"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 2. INV-002 (Invoice Tambahan Selisih Pengganti) -->
                    <template x-for="inv in getAdditionalInvoices(selectedOrderDetail)" :key="inv.id">
                        <div :id="'invoice-card-additional-' + inv.id" class="p-3.5 rounded-xl border border-amber-300 bg-[#FFFDF7] space-y-2.5">
                            <div class="flex justify-between items-start">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold text-zinc-600 text-sm leading-none mt-0.5">+</span>
                                    <div>
                                        <h5 class="font-mono font-bold text-xs text-zinc-950" x-text="inv.invoice_number">INV-002</h5>
                                        <span class="text-[10px] text-zinc-400 block" x-text="t('invoice_diff_sub', 'Invoice Tambahan (Selisih Pengganti)')">Invoice Tambahan (Selisih Pengganti)</span>
                                    </div>
                                </div>
                                <span 
                                    class="text-[9px] font-bold px-2.5 py-0.5 rounded-full"
                                    :class="inv.status === 'paid' ? 'bg-[#E8F8F0] text-[#00A862]' : 'bg-[#FEF3C7] text-[#D97706]'"
                                    x-text="inv.status === 'paid' ? t('status_paid', 'Lunas') : t('status_unpaid', 'Menunggu Pembayaran')">
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-xs pt-1 border-t border-amber-200/50">
                                <span class="text-zinc-600" x-text="t('diff_amount_label', 'Selisih harga pengganti')">Selisih harga pengganti</span>
                                <span class="font-bold text-zinc-950 tabular text-sm" x-text="formatRupiah(inv.amount)"></span>
                            </div>
                            <template x-if="inv.status !== 'paid'">
                                <div class="text-[11px] text-amber-700 font-medium flex items-center gap-1.5 pt-0.5">
                                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span x-text="'Jatuh tempo: ' + getDiffCountdown(inv).text + ' lagi'"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 3. INV-SHIPPING (Invoice Pengiriman) -->
                    <div id="invoice-card-shipping" class="p-3.5 rounded-xl border border-dashed border-amber-300 bg-[#FFFDF0] flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                            <div>
                                <h5 class="font-mono font-bold text-xs text-amber-950">INV-SHIPPING</h5>
                                <span class="text-[10px] text-amber-700 block" x-text="t('invoice_shipping_sub', 'Ditentukan terpisah oleh Admin')">Ditentukan terpisah oleh Admin</span>
                            </div>
                        </div>
                        <template x-if="!getShippingInvoice(selectedOrderDetail)">
                            <span class="text-xs font-bold text-amber-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span x-text="t('status_calculating', 'Menghitung...')">Menghitung...</span>
                            </span>
                        </template>
                        <template x-if="getShippingInvoice(selectedOrderDetail)">
                            <span 
                                class="text-[9px] font-bold px-2 py-0.5 rounded-full"
                                :class="getShippingInvoice(selectedOrderDetail).status === 'paid' ? 'bg-[#E8F8F0] text-[#00A862]' : 'bg-blue-100 text-blue-800'"
                                x-text="getShippingInvoice(selectedOrderDetail).status === 'paid' ? t('status_paid', 'Lunas') : formatRupiah(getShippingInvoice(selectedOrderDetail).amount)">
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Dashed Divider & Total Tagihan -->
                <div class="border-t border-dashed border-zinc-200 pt-3 flex justify-between items-center">
                    <div>
                        <h5 class="text-sm font-extrabold text-zinc-950" x-text="t('total_bill_label', 'Total Tagihan')">Total Tagihan</h5>
                        <span class="text-[11px] text-zinc-400 font-normal block" x-text="t('not_inc_shipping', '*Belum termasuk ongkir')">*Belum termasuk ongkir</span>
                    </div>
                    <span class="text-xl font-black text-[#1657FF] tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.grand_total || selectedOrderDetail.pricing?.product_total || 0)"></span>
                </div>
            </div>

            <!-- Card 8: Metode Pembayaran (Matching Reference with QRIS Badge) -->
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 flex items-center justify-between shadow-xs">
                <span class="text-xs font-semibold text-zinc-600" x-text="t('payment_method_label', 'Metode Pembayaran')">Metode Pembayaran</span>
                <span class="px-2.5 py-1 rounded-lg bg-[#F3E8FF] text-[#7E22CE] text-xs font-black flex items-center gap-1.5 tracking-wide">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>QRIS</span>
                </span>
            </div>

            <!-- Card 9: Action Buttons (Stacked Vertically Matching Reference) -->
            <div class="space-y-2.5 pt-1">
                <!-- 1. Download Invoice Terpilih (PDF) -->
                <button 
                    @click="openInvoiceDownloadModal(selectedOrderDetail)" 
                    class="w-full py-3 bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-800 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs min-h-[44px] active:scale-[0.99]">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span x-text="t('download_selected_invoice_pdf', 'Download Invoice Terpilih (PDF)')">Download Invoice Terpilih (PDF)</span>
                </button>

                <!-- 2. Hubungi Penjual via WhatsApp -->
                <a 
                    :href="selectedOrderDetail.actions?.whatsapp_contact_url || ('https://wa.me/6281200000001?text=Halo%20Admin%2C%20tanya%20pesanan%20' + selectedOrderDetail.order_number)" 
                    target="_blank" 
                    class="w-full py-3 bg-[#00D06C] hover:bg-[#00B85F] text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs min-h-[44px] active:scale-[0.99]">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.082-2.146-.517-1.748-.724-2.883-2.493-2.971-2.609-.088-.116-.713-.949-.713-1.808s.45-1.282.61-1.455c.16-.174.348-.218.464-.218.116 0 .232.002.333.007.106.005.249-.041.389.296.145.348.493 1.204.536 1.292.043.087.072.19.014.305-.058.116-.087.188-.174.29-.087.101-.183.227-.261.305-.088.087-.18.181-.077.357.102.174.455.75 1.047 1.278.761.678 1.403.888 1.602.986.199.098.316.084.433-.051.117-.135.5-.584.633-.787.133-.203.267-.17.449-.102.183.069 1.157.546 1.355.645.199.1.332.149.38.232.049.083.049.48-.095.885z"/></svg>
                    <span x-text="t('contact_seller_wa', 'Hubungi Penjual via WhatsApp')">Hubungi Penjual via WhatsApp</span>
                </a>

                <!-- 3. Bayar Biaya Pengiriman -->
                <template x-if="!getShippingInvoice(selectedOrderDetail) || getShippingInvoice(selectedOrderDetail).status === 'paid'">
                    <button disabled class="w-full py-3 bg-[#F3F4F6] text-zinc-400 rounded-xl text-xs font-bold flex items-center justify-center gap-2 cursor-not-allowed min-h-[44px]">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        <span x-text="t('pay_shipping_fee_btn', 'Bayar Biaya Pengiriman')">Bayar Biaya Pengiriman</span>
                    </button>
                </template>
                <template x-if="getShippingInvoice(selectedOrderDetail) && getShippingInvoice(selectedOrderDetail).status !== 'paid'">
                    <button 
                        @click="openShippingPayment()" 
                        class="w-full py-3 bg-[#1657FF] hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs min-h-[44px] active:scale-[0.99]">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        <span x-text="t('pay_shipping_fee_btn', 'Bayar Biaya Pengiriman') + ' (' + formatRupiah(getShippingInvoice(selectedOrderDetail).amount) + ')'">Bayar Biaya Pengiriman</span>
                    </button>
                </template>
            </div>

            <!-- Card 10: Additional Features (Trip & Bagasian, Review & Admin simulator collapsed) -->
            <div class="pt-2 space-y-3">
                <!-- Completed Actions: Review & Reorder -->
                <template x-if="selectedOrderDetail.status === 'completed'">
                    <div class="flex gap-2">
                        <button 
                            @click="openReviewModal(selectedOrderDetail)" 
                            class="flex-1 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-semibold transition min-h-[44px]" x-text="t('review_product_btn', 'Ulas Produk')">
                            Ulas Produk
                        </button>
                        <button 
                            @click="reorderItems(selectedOrderDetail)" 
                            class="flex-1 py-2.5 bg-[#E60012] hover:bg-red-700 text-white rounded-xl text-xs font-bold transition min-h-[44px]" x-text="t('buy_again_btn', 'Beli Lagi')">
                            Beli Lagi
                        </button>
                    </div>
                </template>

                <!-- Admin & Simulation Drawer (Foldable for Developer Testing) -->
                <details class="bg-zinc-100 border border-zinc-200 rounded-xl p-3 text-xs space-y-2 group">
                    <summary class="font-bold text-zinc-700 cursor-pointer flex items-center justify-between">
                        <span>🛠️ Panel Simulasi & Testing Admin</span>
                        <svg class="w-3.5 h-3.5 stroke-current fill-none transition-transform group-open:rotate-180" viewBox="0 0 24 24" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </summary>
                    <div class="space-y-3 pt-2">
                        <div>
                            <span class="text-zinc-600 font-bold block mb-1">Ubah Status Pesanan:</span>
                            <div class="flex gap-2">
                                <select x-model="adminSelectedStatus" class="flex-1 text-xs border border-zinc-300 rounded-lg p-2 bg-white font-medium min-h-[40px]">
                                    <option value="pending_payment_product">Pending Bayar Produk</option>
                                    <option value="paid_product">Produk Dibayar (paid_product)</option>
                                    <option value="processing">Diproses (processing)</option>
                                    <option value="packing">Packing (packing)</option>
                                    <option value="ready_for_delivery">Siap Kirim / Ongkir</option>
                                    <option value="shipping_paid">Ongkir Dibayar (shipping_paid)</option>
                                    <option value="delivering">Sedang Dikirim (delivering)</option>
                                    <option value="completed">Pesanan Selesai (completed)</option>
                                </select>
                                <button @click="changeOrderStatus(selectedOrderDetail.id)" class="px-3 py-2 bg-zinc-950 text-white text-xs font-bold rounded-lg transition min-h-[40px]">
                                    Update
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1 border-t border-zinc-200">
                            <button @click="adminSimulateStatus(selectedOrderDetail.id, 'ready_for_delivery', 35000)" class="px-3 py-1.5 bg-white border border-zinc-300 text-zinc-800 text-[11px] font-bold rounded-lg transition">
                                Terbitkan Ongkir Rp 35.000
                            </button>
                        </div>
                    </div>
                </details>
            </div>

        </div>
    </template>
</div>
