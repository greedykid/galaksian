<!-- ========================================================= -->
        <!-- MODAL: SOLUSI BARANG HABIS DI JEPANG (OOS RESOLUTION)      -->
        <!-- ========================================================= -->
        <div x-show="showOosModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-250"
             x-cloak
             class="fixed inset-0 z-50 flex items-end justify-center overflow-hidden" 
             style="display: none;"
             @keydown.window.escape="showOosModal = false">

            <!-- Smooth Backdrop Fade -->
            <div x-show="showOosModal"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-250"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 backdrop-blur-[2px]" 
                 @click="showOosModal = false">
            </div>

            <!-- Sliding Bottom Sheet Panel -->
            <div x-show="showOosModal"
                 x-transition:enter="transition-transform ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition-transform ease-in duration-250 transform"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 :style="getSheetStyle('oos')"
                 class="relative w-full max-w-[430px] bg-white rounded-t-[28px] max-h-[90vh] overflow-hidden flex flex-col shadow-2xl z-10 border-t border-zinc-100">

                <!-- Grab Bar Handle (Swipeable Up/Down when pressed) -->
                <div class="pt-3 pb-1.5 flex flex-col items-center justify-center cursor-grab active:cursor-grabbing select-none touch-none w-full"
                     @touchstart.passive="startSheetDrag('oos', $event)"
                     @mousedown="startSheetDrag('oos', $event)">
                    <div class="w-12 h-1.5 bg-zinc-300 rounded-full hover:bg-zinc-400 active:bg-zinc-500 transition-colors"></div>
                </div>

                <!-- Top Header -->
                <div class="px-4 py-2.5 border-b border-zinc-100 flex justify-between items-center bg-white cursor-grab active:cursor-grabbing select-none"
                     @touchstart.passive="startSheetDrag('oos', $event)"
                     @mousedown="startSheetDrag('oos', $event)">
                    <div>
                        <h3 class="font-extrabold text-sm text-zinc-950" x-text="t('oos_modal_title', 'Solusi Barang Habis di Toko JP')">Solusi Barang Habis di Toko JP</h3>
                        <p class="text-[11px] text-zinc-500" x-text="t('oos_modal_subtitle', 'Pilih opsi pengembalian dana atau penggantian barang')">Pilih opsi pengembalian dana atau penggantian barang</p>
                    </div>
                    <button @click.stop="showOosModal = false" type="button" class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-4 space-y-4 text-xs overflow-y-auto flex-1">
                    <!-- Target Item Info Card -->
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl space-y-2">
                        <div class="flex items-center gap-2 text-red-800 font-bold">
                            <svg class="w-4 h-4 stroke-current fill-none flex-shrink-0" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span x-text="t('oos_item_out_stock', 'Stok Habis di Toko Jepang')">Stok Habis di Toko Jepang</span>
                        </div>
                        <div class="flex justify-between items-center pt-1 border-t border-red-100">
                            <div>
                                <p class="font-bold text-zinc-900" x-text="oosSelectedItem?.product_name || oosSelectedItem?.name || 'Produk Pesanan'"></p>
                                <span class="text-[11px] text-zinc-500 tabular" x-text="(oosSelectedItem?.qty || 1) + ' pcs @ ' + formatRupiah(getOosOldItemPrice())"></span>
                            </div>
                            <span class="font-extrabold text-zinc-950 tabular text-sm" x-text="formatRupiah(oosSelectedItem?.subtotal || (getOosOldItemPrice() * (oosSelectedItem?.qty || 1)))"></span>
                        </div>
                        <p class="text-[11px] text-zinc-600 pt-1 leading-relaxed" x-text="t('oos_explanation', 'Barang ini tidak tersedia saat personal shopper berbelanja di Jepang. Silakan tentukan opsi penanganan di bawah:')">
                            Barang ini tidak tersedia saat personal shopper berbelanja di Jepang. Silakan tentukan opsi penanganan di bawah:
                        </p>
                    </div>

                    <!-- Tab Switcher: Refund vs Ganti Produk -->
                    <div class="grid grid-cols-2 bg-zinc-100 p-1 rounded-xl text-xs font-bold text-center border border-zinc-200/60">
                        <button 
                            @click="oosChoice = 'refund'" 
                            :class="oosChoice === 'refund' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2.5 rounded-lg transition min-h-[44px]">
                            <span x-text="t('oos_opt_refund_tab', '1. Refund Dana')">1. Refund Dana</span>
                        </button>
                        <button 
                            @click="oosChoice = 'replace'" 
                            :class="oosChoice === 'replace' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2.5 rounded-lg transition min-h-[44px]">
                            <span x-text="t('oos_opt_replace_tab', '2. Ganti Produk Lain')">2. Ganti Produk Lain</span>
                        </button>
                    </div>

                    <!-- OPTION 1: REFUND DANA BARANG INI SAJA -->
                    <div x-show="oosChoice === 'refund'" class="space-y-3 bg-zinc-50 p-3.5 rounded-xl border border-zinc-200">
                        <h4 class="font-bold text-zinc-950 flex items-center gap-1.5">
                            <svg class="w-4 h-4 stroke-current fill-none text-zinc-700" viewBox="0 0 24 24" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            <span x-text="t('oos_opt_refund_header', 'Pengembalian Dana (Hanya Seharga Barang Habis Ini)')">Pengembalian Dana (Hanya Seharga Barang Habis Ini)</span>
                        </h4>
                        <p class="text-[11px] text-zinc-600 leading-relaxed" x-text="t('oos_refund_desc', 'Dana seharga barang yang habis ini saja akan dikembalikan ke saldo/rekening Anda. Pesanan barang lainnya yang tersedia tetap diproses dan dikirimkan.')">
                            Dana seharga barang yang habis ini saja akan dikembalikan ke saldo/rekening Anda. Pesanan barang lainnya yang tersedia tetap diproses dan dikirimkan.
                        </p>
                        <div class="p-2.5 bg-white rounded-lg border border-zinc-200 flex justify-between items-center">
                            <span class="text-zinc-600 font-medium" x-text="t('refund_amount_label', 'Nominal Refund:')">Nominal Refund:</span>
                            <span class="font-extrabold text-[#E60012] tabular text-base" x-text="formatRupiah(oosSelectedItem?.subtotal || (getOosOldItemPrice() * (oosSelectedItem?.qty || 1)))"></span>
                        </div>
                        <button 
                            @click="resolveOosRefund()" 
                            class="w-full py-3 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg shadow-sm transition min-h-[44px]" x-text="t('confirm_refund_item_only', 'Konfirmasi Refund Barang Ini Saja')">
                            Konfirmasi Refund Barang Ini Saja
                        </button>
                    </div>

                    <!-- OPTION 2: GANTI PRODUK LAIN YANG TERSEDIA -->
                    <div x-show="oosChoice === 'replace'" class="space-y-3 bg-zinc-50 p-3.5 rounded-xl border border-zinc-200">
                        <h4 class="font-bold text-zinc-950 flex items-center gap-1.5">
                            <svg class="w-4 h-4 stroke-current fill-none text-zinc-700" viewBox="0 0 24 24" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"></path></svg>
                            <span x-text="t('choose_replacement_title', 'Pilih Produk Pengganti yang Tersedia')">Pilih Produk Pengganti yang Tersedia</span>
                        </h4>
                        <p class="text-[11px] text-zinc-600" x-html="t('choose_replacement_desc')"></p>

                        <!-- Candidate Products List -->
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            <template x-for="cand in oosReplacementCandidates" :key="cand.id">
                                <div 
                                    @click="oosReplacementProduct = cand" 
                                    :class="oosReplacementProduct?.id === cand.id ? 'border-zinc-950 bg-white ring-2 ring-zinc-950' : 'border-zinc-200 bg-white hover:border-zinc-300'"
                                    class="p-2.5 border rounded-lg flex items-center justify-between cursor-pointer transition min-h-[44px]">
                                    <div class="flex items-center gap-2.5">
                                        <img :src="cand.image" class="w-10 h-10 rounded-md object-cover bg-zinc-100 border border-zinc-200 flex-shrink-0" alt="Replacement">
                                        <div>
                                            <h5 class="font-bold text-zinc-900 text-xs line-clamp-1" x-text="cand.name"></h5>
                                            <span class="text-[11px] text-zinc-500 tabular" x-text="formatRupiah(cand.price)"></span>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 pl-2">
                                        <template x-if="(cand.price - getOosOldItemPrice()) > 0">
                                            <span class="text-[10px] font-extrabold text-[#E60012] tabular block" x-text="'+ ' + formatRupiah(cand.price - getOosOldItemPrice())"></span>
                                        </template>
                                        <template x-if="(cand.price - getOosOldItemPrice()) < 0">
                                            <span class="text-[10px] font-bold text-emerald-700 tabular block" x-text="'Refund ' + formatRupiah(getOosOldItemPrice() - cand.price)"></span>
                                        </template>
                                        <template x-if="(cand.price - getOosOldItemPrice()) === 0">
                                            <span class="text-[10px] font-medium text-zinc-500 block" x-text="t('same_price', 'Harga Sama')">Harga Sama</span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Price Difference Box -->
                        <div class="p-3 bg-white rounded-lg border border-zinc-200 space-y-1.5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500" x-text="t('old_item_price_label', 'Harga Barang Lama:')">Harga Barang Lama:</span>
                                <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(getOosOldItemPrice())"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-zinc-500" x-text="t('replacement_item_price_label', 'Harga Produk Pengganti:')">Harga Produk Pengganti:</span>
                                <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(oosReplacementProduct?.price || 0)"></span>
                            </div>
                            <div class="pt-1.5 border-t border-zinc-100 flex justify-between items-center">
                                <span class="font-bold text-zinc-900" x-text="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) > 0 ? t('underpayment_invoice', 'Kurang Bayar (Invoice Baru):') : (((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) < 0 ? t('overpayment_refund', 'Lebih Bayar (Akan Direfund):') : t('price_diff_label', 'Selisih Biaya:'))"></span>
                                <span 
                                    :class="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) > 0 ? 'text-[#E60012] font-black text-sm' : (((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) < 0 ? 'text-emerald-700 font-black text-sm' : 'text-zinc-600 font-bold')"
                                    class="tabular"
                                    x-text="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) > 0 ? '+ ' + formatRupiah((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) : (((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) < 0 ? '- ' + formatRupiah(getOosOldItemPrice() - (oosReplacementProduct?.price || 0)) : t('zero_same_price', 'Rp 0 (Harga Sama)'))">
                                </span>
                            </div>
                        </div>

                        <!-- Notice if more expensive (Invoice Tambahan / Offset) -->
                        <template x-if="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) > 0">
                            <div class="space-y-2">
                                <template x-if="getTotalRefundAmount() >= ((oosReplacementProduct?.price || 0) - getOosOldItemPrice())">
                                    <div class="text-[11px] text-emerald-900 bg-emerald-50 border border-emerald-200 p-2.5 rounded-lg leading-relaxed flex items-start gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <div>
                                            <span class="font-bold block text-emerald-950" x-text="t('auto_settlement_offset_title', 'Lunas Otomatis (Skema Offset)')">Lunas Otomatis (Skema Offset)</span>
                                            <span x-html="t('auto_settlement_offset_desc_p1', 'Saldo refund Anda (') + '<strong class=\'tabular\'>' + formatRupiah(getTotalRefundAmount()) + '</strong>' + t('auto_settlement_offset_desc_p2', ') mencukupi untuk menutup selisih harga ini. Tagihan tambahan langsung lunas otomatis dan sisa refund Anda menjadi ') + '<strong class=\'tabular\'>' + formatRupiah(getTotalRefundAmount() - ((oosReplacementProduct?.price || 0) - getOosOldItemPrice())) + '</strong>.'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="getTotalRefundAmount() > 0 && getTotalRefundAmount() < ((oosReplacementProduct?.price || 0) - getOosOldItemPrice())">
                                    <div class="text-[11px] text-amber-900 bg-amber-50 border border-amber-200 p-2.5 rounded-lg leading-relaxed flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>
                                        <div>
                                            <span class="font-bold block text-amber-950" x-text="t('partial_refund_offset_title', 'Potong Saldo Refund Sebagian')">Potong Saldo Refund Sebagian</span>
                                            <span x-html="t('partial_refund_offset_desc_p1', 'Saldo refund Anda (') + '<strong class=\'tabular\'>' + formatRupiah(getTotalRefundAmount()) + '</strong>' + t('partial_refund_offset_desc_p2', ') dipakai untuk memotong tagihan ini. Anda hanya perlu membayar sisa kekurangan sebesar ') + '<strong class=\'tabular font-bold text-[#E60012]\'>' + formatRupiah(((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) - getTotalRefundAmount()) + '</strong>.'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="getTotalRefundAmount() === 0">
                                    <div class="text-[11px] text-zinc-700 bg-amber-50 border border-amber-200 p-2.5 rounded-lg leading-relaxed flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                        <span x-html="'<strong>' + t('new_additional_invoice_label', 'Invoice Tambahan Baru') + '</strong> ' + t('amounting_to', 'sebesar') + ' <span class=\'font-bold tabular text-zinc-950\'>' + formatRupiah((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) + '</span> ' + t('auto_issued_notice', 'akan otomatis diterbitkan di pesanan ini untuk pelunasan selisih harga produk pengganti.')"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Notice if CHEAPER (Kelebihan Bayar / Refund Otomatis) -->
                        <template x-if="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) < 0">
                            <div class="text-[11px] text-emerald-900 bg-emerald-50 border border-emerald-200 p-2.5 rounded-lg leading-relaxed flex items-start gap-2">
                                <svg class="w-4 h-4 text-emerald-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span x-html="t('replacement_cheaper_p1', 'Produk pengganti lebih hemat') + ' <strong><span class=\'tabular font-bold\'>' + formatRupiah(getOosOldItemPrice() - (oosReplacementProduct?.price || 0)) + '</span></strong>. ' + t('replacement_cheaper_p2', 'Kelebihan pembayaran dari pesanan awal Anda aman dan otomatis diajukan') + ' <strong>' + t('partial_refund_bold', 'pengembalian dana (partial refund)') + '</strong> ' + t('replacement_cheaper_p3', 'ke rekening/saldo Anda.')"></span>
                            </div>
                        </template>

                        <!-- Notice if SAME PRICE -->
                        <template x-if="((oosReplacementProduct?.price || 0) - getOosOldItemPrice()) === 0">
                            <div class="text-[11px] text-zinc-700 bg-zinc-50 border border-zinc-200 p-2.5 rounded-lg leading-relaxed flex items-start gap-2">
                                <svg class="w-4 h-4 text-zinc-600 stroke-current fill-none shrink-0 mt-0.5" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                <span x-text="t('replacement_same_price_desc', 'Harga produk pengganti sama dengan pesanan awal. Tidak ada biaya tambahan ataupun pengembalian dana yang diperlukan.')">Harga produk pengganti sama dengan pesanan awal. Tidak ada biaya tambahan ataupun pengembalian dana yang diperlukan.</span>
                            </div>
                        </template>

                        <button 
                            @click="resolveOosReplacement()" 
                            class="w-full py-3 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg shadow-sm transition min-h-[44px]">
                            <span x-text="getOosButtonText()"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
