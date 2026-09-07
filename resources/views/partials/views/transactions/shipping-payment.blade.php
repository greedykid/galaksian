<!-- ========================================================= -->
            <!-- SUB-VIEW: BAYAR BIAYA PENGIRIMAN (REFERENCE DESIGN)       -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'shipping-payment'" class="pb-24 bg-[#FAFAF9] min-h-[85vh]" x-cloak>
                <!-- Blue Top Header (Consistent Royal Blue #1657FF) -->
                <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3 shadow-xs -mx-px w-[calc(100%+2px)]">
                    <div class="flex items-center gap-3">
                        <button @click="closeShippingPayment()" class="w-9 h-9 rounded-xl border border-white/30 bg-white/15 text-white flex items-center justify-center hover:bg-white/25 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <div class="leading-tight">
                            <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('shipping_payment_page_title', 'Bayar Biaya Pengiriman')">Bayar Biaya Pengiriman</h1>
                            <span class="block text-[11px] font-mono text-white/80 font-medium mt-0.5" x-text="selectedOrderDetail ? ('#' + selectedOrderDetail.order_number) : ''"></span>
                        </div>
                    </div>
                </div>

                <div class="px-4 mt-3 space-y-3">
                    <!-- Info Banner Card -->
                    <div class="bg-[#F0FDF4] border border-emerald-200 rounded-2xl p-3.5 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2.2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
                        <p class="text-[11px] text-emerald-800 font-medium leading-relaxed" x-text="t('shipping_unlocked_banner', 'Biaya tambahan telah lunas. Selesaikan pembayaran ongkos kirim untuk melanjutkan pengiriman paketmu.')">Biaya tambahan telah lunas. Selesaikan pembayaran ongkos kirim untuk melanjutkan pengiriman paketmu.</p>
                    </div>

                    <!-- Rincian Biaya Pengiriman (Satu Card Terpadu: Jastip + Lokal + Total) -->
                    <div class="bg-white rounded-2xl border border-zinc-200/90 p-4 shadow-2xs">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3" x-text="t('shipping_cost_breakdown_title', 'Rincian Biaya Pengiriman')">Rincian Biaya Pengiriman</h3>
                        <div class="space-y-3">
                            <!-- Jastip Shipping -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="1.8"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-zinc-900" x-text="t('shipping_fee_jastip', 'Biaya Pengiriman Jastip')">Biaya Pengiriman Jastip</h4>
                                        <p class="text-[10px] text-zinc-500" x-text="t('shipping_jastip_desc', 'Tokyo, Jepang → Gudang Indonesia')">Tokyo, Jepang → Gudang Indonesia</p>
                                        <p class="text-[10px] text-zinc-500 font-medium flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span x-text="t('shipping_jastip_eta', 'JNT Express International · ~7–14 hari kerja')">JNT Express International · ~7–14 hari kerja</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="font-bold text-zinc-900 tabular shrink-0" x-text="formatRupiah(selectedOrderDetail?.pricing?.shipping_jastip_amount || 0)"></span>
                            </div>

                            <!-- Local Shipping -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="1.8"><path d="M4 6h16v12H4z"></path><path d="M4 11h16"></path><path d="M8 6V4M16 6V4"></path><circle cx="7" cy="15" r="1"></circle><circle cx="17" cy="15" r="1"></circle></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-zinc-900" x-text="t('shipping_fee_local', 'Biaya Pengiriman Lokal')">Biaya Pengiriman Lokal</h4>
                                        <p class="text-[10px] text-zinc-500" x-text="t('shipping_local_desc', 'Gudang Indonesia → Alamat Penerima')">Gudang Indonesia → Alamat Penerima</p>
                                        <p class="text-[10px] text-zinc-500 font-medium flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span x-text="t('shipping_local_eta', 'JNE REG · ~2–3 hari kerja')">JNE REG · ~2–3 hari kerja</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="font-bold text-zinc-900 tabular shrink-0" x-text="formatRupiah(selectedOrderDetail?.pricing?.shipping_local_amount || 0)"></span>
                            </div>

                            <!-- Total Shipping Fee (baris pemisah, bukan card) -->
                            <div class="flex items-center justify-between gap-3 pt-3 border-t border-zinc-100">
                                <span class="text-xs font-bold text-zinc-900" x-text="t('total_shipping_fee', 'Total Biaya Pengiriman')">Total Biaya Pengiriman</span>
                                <span class="font-extrabold text-[#1657FF] text-base tabular" x-text="formatRupiah(selectedOrderDetail?.pricing?.shipping_total || 0)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Tujuan -->
                    <div class="bg-white rounded-2xl border border-zinc-200/90 p-4 shadow-2xs">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3" x-text="t('ship_destination_label', 'Alamat Tujuan')">Alamat Tujuan</h3>
                        <div class="flex items-start gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-zinc-900" x-text="selectedOrderDetail?.address?.recipient_name || 'Penerima'"></h4>
                                <p class="text-[11px] text-zinc-500 mt-0.5 leading-snug" x-text="selectedOrderDetail?.address ? (selectedOrderDetail.address.address + ', ' + (selectedOrderDetail.address.city || '') + (selectedOrderDetail.address.postal_code ? ' ' + selectedOrderDetail.address.postal_code : '')) : ''"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-white rounded-2xl border border-zinc-200/90 p-4 shadow-2xs">
                        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-3" x-text="t('payment_method_label', 'Metode Pembayaran')">Metode Pembayaran</h3>
                        <div class="space-y-2">
                            <!-- Virtual Account -->
                            <div
                                @click="shippingPaymentMethod = 'virtual_account'"
                                :class="shippingPaymentMethod === 'virtual_account' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'"
                                class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1657FF] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><line x1="3" y1="21" x2="21" y2="21"></line><line x1="3" y1="10" x2="21" y2="10"></line><polyline points="5 6 12 3 19 6"></polyline><line x1="4" y1="10" x2="4" y2="21"></line><line x1="20" y1="10" x2="20" y2="21"></line></svg>
                                    </div>
                                    <div>
                                        <h4 :class="shippingPaymentMethod === 'virtual_account' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs" x-text="t('pay_method_va', 'Virtual Account Bank')">Virtual Account Bank</h4>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_va_desc', 'BCA, Mandiri, BRI, BNI')">BCA, Mandiri, BRI, BNI</p>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="shippingPaymentMethod === 'virtual_account' ? 'border-[#1657FF]' : 'border-zinc-300'">
                                    <template x-if="shippingPaymentMethod === 'virtual_account'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                                    </template>
                                </div>
                            </div>

                            <!-- QRIS -->
                            <div
                                @click="shippingPaymentMethod = 'qris'"
                                :class="shippingPaymentMethod === 'qris' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'"
                                class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    </div>
                                    <div>
                                        <h4 :class="shippingPaymentMethod === 'qris' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs" x-text="t('pay_method_qris', 'QRIS (Semua E-Wallet & Mobile Banking)')">QRIS (Semua E-Wallet &amp; Mobile Banking)</h4>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_qris_desc', 'GoPay, BCA, OVO, ShopeePay, Dana, Mandiri')">GoPay, BCA, OVO, ShopeePay, Dana, Mandiri</p>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="shippingPaymentMethod === 'qris' ? 'border-[#1657FF]' : 'border-zinc-300'">
                                    <template x-if="shippingPaymentMethod === 'qris'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                                    </template>
                                </div>
                            </div>

                            <!-- Transfer Manual -->
                            <div
                                @click="shippingPaymentMethod = 'manual'"
                                :class="shippingPaymentMethod === 'manual' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'"
                                class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V10M19 21V10M9 21V10M15 21V10"></path><path d="M12 3l7 4v3H5V7l7-4z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 :class="shippingPaymentMethod === 'manual' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs" x-text="t('pay_method_manual', 'Transfer Manual Bank')">Transfer Manual Bank</h4>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_manual_desc', 'Konfirmasi bukti transfer ke CS')">Konfirmasi bukti transfer ke CS</p>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="shippingPaymentMethod === 'manual' ? 'border-[#1657FF]' : 'border-zinc-300'">
                                    <template x-if="shippingPaymentMethod === 'manual'">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
