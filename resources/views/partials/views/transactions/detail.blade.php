<!-- ========================================================= -->
            <!-- SUBVIEW: DETAIL TRANSAKSI (ANTISLOP EXECUTIVE GRADE)      -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'order-detail'" class="space-y-4 w-full max-w-full">
                <!-- Sticky Top Header for Detail Transaksi -->
                <div class="sticky top-0 z-30 px-4 py-2.5 bg-[#1657FF] text-white flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
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
                        <!-- Card 1: Order Status Header -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-2.5 shadow-xs">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] text-zinc-400 block font-medium uppercase tracking-wider" x-text="t('order_time_label', 'Waktu Pemesanan')">Waktu Pemesanan</span>
                                    <span class="text-xs font-semibold text-zinc-900" x-text="formatDateTime(selectedOrderDetail.timestamps?.created_at || selectedOrderDetail.created_at)"></span>
                                </div>
                                <span 
                                    :class="getOrderStatusColor(selectedOrderDetail.status)" 
                                    class="text-[10px] font-bold px-2.5 py-1 rounded border" 
                                    x-text="getOrderStatusLabel(selectedOrderDetail.status)">
                                </span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100 text-xs">
                                <span class="text-zinc-500 font-medium" x-text="t('order_number_label', 'Nomor Pesanan:')">Nomor Pesanan:</span>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-zinc-900" x-text="selectedOrderDetail.order_number"></span>
                                    <button @click="copyToClipboard(selectedOrderDetail.order_number)" class="text-zinc-500 hover:text-zinc-900 text-xs font-medium flex items-center gap-1 bg-zinc-100 px-2 py-1 rounded min-h-[32px]">
                                        <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        <span x-text="t('copy_btn', 'Salin')">Salin</span>
                                    </button>
                                </div>
                            </div>
                            <template x-if="selectedOrderDetail.notes">
                                <div class="pt-2 border-t border-zinc-100 text-xs">
                                    <span class="text-zinc-400 text-[11px] block" x-text="t('order_notes_label', 'Catatan Pesanan:')">Catatan Pesanan:</span>
                                    <p class="text-zinc-700 italic" x-text="selectedOrderDetail.notes"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Card 2: 6-Step Visual Delivery Progress Stepper (VERTICAL ATAS KE BAWAH) -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-4 shadow-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-zinc-100">
                                <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-2">
                                    <svg class="w-4 h-4 stroke-current fill-none text-zinc-700" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                    <span x-text="t('order_status', 'Status Pesanan')">Status Pesanan</span>
                                </h4>
                                <span 
                                    :class="getOrderStatusColor(selectedOrderDetail.status)" 
                                    class="text-[10px] font-bold px-2 py-0.5 rounded border" 
                                    x-text="getOrderStatusLabel(selectedOrderDetail.status)">
                                </span>
                            </div>

                            <!-- Vertical Timeline -->
                            <div class="space-y-0 relative pl-1">
                                <!-- Step 1: Dipesan -->
                                <div class="flex items-start gap-3 relative pb-5">
                                    <!-- Vertical Connector Line -->
                                    <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="isStepPassed(selectedOrderDetail.status, 2) ? 'bg-zinc-950' : 'bg-zinc-200'"></div>
                                    <!-- Node Circle -->
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 1) ? (isStepActive(selectedOrderDetail.status, 1) ? 'bg-zinc-950 text-white ring-4 ring-zinc-200' : 'bg-zinc-950 text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 2)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 2)">
                                            <span>1</span>
                                        </template>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 1) ? 'text-zinc-950' : 'text-zinc-400'" x-text="t('step_1_title', '1. Dipesan & Menunggu Konfirmasi')">1. Dipesan & Menunggu Konfirmasi</h5>
                                            <template x-if="selectedOrderDetail.timestamps?.created_at">
                                                <span class="text-[10px] text-zinc-400 tabular" x-text="formatDate(selectedOrderDetail.timestamps.created_at)"></span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 1) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_1_desc', 'Pesanan dibuat dan tagihan produk diterbitkan untuk diproses ke antrian jastip.')">
                                            Pesanan dibuat dan tagihan produk diterbitkan untuk diproses ke antrian jastip.
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 2: Beli JP -->
                                <div class="flex items-start gap-3 relative pb-5">
                                    <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="isStepPassed(selectedOrderDetail.status, 3) ? 'bg-zinc-950' : 'bg-zinc-200'"></div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 2) ? (isStepActive(selectedOrderDetail.status, 2) ? 'bg-zinc-950 text-white ring-4 ring-zinc-200' : 'bg-zinc-950 text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 3)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 3)">
                                            <span>2</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 2) ? 'text-zinc-950' : 'text-zinc-400'" x-text="t('step_2_title', '2. Pembelian di Jepang (Jastip)')">2. Pembelian di Jepang (Jastip)</h5>
                                            <template x-if="isStepActive(selectedOrderDetail.status, 2)">
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" x-text="t('in_progress', 'Sedang Berjalan')">Sedang Berjalan</span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 2) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_2_desc', 'Personal shopper membelikan produk pesanan langsung di toko resmi di Tokyo/Jepang.')">
                                            Personal shopper membelikan produk pesanan langsung di toko resmi di Tokyo/Jepang.
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 3: Packing -->
                                <div class="flex items-start gap-3 relative pb-5">
                                    <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="isStepPassed(selectedOrderDetail.status, 4) ? 'bg-zinc-950' : 'bg-zinc-200'"></div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 3) ? (isStepActive(selectedOrderDetail.status, 3) ? 'bg-zinc-950 text-white ring-4 ring-zinc-200' : 'bg-zinc-950 text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 4)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 4)">
                                            <span>3</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 3) ? 'text-zinc-950' : 'text-zinc-400'" x-text="t('step_3_title', '3. Packing & Penimbangan Bagasian')">3. Packing & Penimbangan Bagasian</h5>
                                            <template x-if="isStepActive(selectedOrderDetail.status, 3)">
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" x-text="t('in_progress', 'Sedang Berjalan')">Sedang Berjalan</span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 3) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_3_desc', 'Barang dicek, ditimbang, dan dimasukkan ke dalam koper/bagasi penerbangan jastip.')">
                                            Barang dicek, ditimbang, dan dimasukkan ke dalam koper/bagasi penerbangan jastip.
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 4: Siap Kirim (Tagihan Ongkir) -->
                                <div class="flex items-start gap-3 relative pb-5">
                                    <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="isStepPassed(selectedOrderDetail.status, 5) ? 'bg-zinc-950' : 'bg-zinc-200'"></div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 4) ? (isStepActive(selectedOrderDetail.status, 4) ? 'bg-zinc-950 text-white ring-4 ring-zinc-200' : 'bg-zinc-950 text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 5)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 5)">
                                            <span>4</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 4) ? 'text-zinc-950' : 'text-zinc-400'" x-text="t('step_4_title', '4. Siap Kirim & Pelunasan Ongkir')">4. Siap Kirim & Pelunasan Ongkir</h5>
                                            <template x-if="isStepActive(selectedOrderDetail.status, 4)">
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800" x-text="t('awaiting_shipping_fee', 'Menunggu Ongkir')">Menunggu Ongkir</span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 4) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_4_desc', 'Barang tiba di Indonesia. Invoice ongkos kirim resmi diterbitkan untuk pelunasan biaya pengiriman.')">
                                            Barang tiba di Indonesia. Invoice ongkos kirim resmi diterbitkan untuk pelunasan biaya pengiriman.
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 5: Dikirim -->
                                <div class="flex items-start gap-3 relative pb-5">
                                    <div class="absolute left-3 top-6 bottom-0 w-0.5" :class="isStepPassed(selectedOrderDetail.status, 6) ? 'bg-zinc-950' : 'bg-zinc-200'"></div>
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 5) ? (isStepActive(selectedOrderDetail.status, 5) ? 'bg-zinc-950 text-white ring-4 ring-zinc-200' : 'bg-zinc-950 text-white') : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 6)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 6)">
                                            <span>5</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 5) ? 'text-zinc-950' : 'text-zinc-400'" x-text="t('step_5_title', '5. Dalam Pengiriman Domestik')">5. Dalam Pengiriman Domestik</h5>
                                            <template x-if="isStepActive(selectedOrderDetail.status, 5)">
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800" x-text="t('with_courier', 'Diantar Kurir')">Diantar Kurir</span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 5) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_5_desc', 'Paket diserahkan ke jasa kurir domestik menuju alamat penerima Anda.')">
                                            Paket diserahkan ke jasa kurir domestik menuju alamat penerima Anda.
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 6: Selesai -->
                                <div class="flex items-start gap-3 relative">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold z-10 flex-shrink-0 transition"
                                        :class="isStepPassed(selectedOrderDetail.status, 6) ? 'bg-emerald-600 text-white shadow' : 'bg-zinc-100 text-zinc-400 border border-zinc-200'">
                                        <template x-if="isStepPassed(selectedOrderDetail.status, 6)">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </template>
                                        <template x-if="!isStepPassed(selectedOrderDetail.status, 6)">
                                            <span>6</span>
                                        </template>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <h5 class="text-xs font-bold" :class="isStepPassed(selectedOrderDetail.status, 6) ? 'text-emerald-700 font-extrabold' : 'text-zinc-400'" x-text="t('step_6_title', '6. Pesanan Selesai')">6. Pesanan Selesai</h5>
                                        <p class="text-[11px] mt-0.5 leading-relaxed" :class="isStepPassed(selectedOrderDetail.status, 6) ? 'text-zinc-600' : 'text-zinc-400'" x-text="t('step_6_desc', 'Barang pesanan telah sampai dengan aman dan transaksi dinyatakan selesai.')">
                                            Barang pesanan telah sampai dengan aman dan transaksi dinyatakan selesai.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status History Audit Logs -->
                            <template x-if="selectedOrderDetail.status_histories && selectedOrderDetail.status_histories.length > 0">
                                <div class="mt-4 pt-3 border-t border-zinc-100 space-y-2">
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block" x-text="t('transaction_history_notes', 'Catatan Riwayat Transaksi:')">Catatan Riwayat Transaksi:</span>
                                    <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                        <template x-for="history in selectedOrderDetail.status_histories" :key="history.id">
                                            <div class="flex items-start gap-2 text-xs bg-zinc-50 p-2.5 rounded-lg border border-zinc-100">
                                                <div class="w-1.5 h-1.5 rounded-full bg-zinc-950 mt-1.5 flex-shrink-0"></div>
                                                <div class="flex-1">
                                                    <p class="text-zinc-900 font-medium text-[11px]" x-text="history.note"></p>
                                                    <span class="text-[10px] text-zinc-400 tabular" x-text="formatDateTime(history.created_at)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Card 3: Trip & Flight Info (Bagasian) -->
                        <template x-if="selectedOrderDetail.trip">
                            <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-2.5 shadow-xs">
                                <div class="flex justify-between items-center">
                                    <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 stroke-current fill-none text-zinc-600" viewBox="0 0 24 24" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                                        <span x-text="t('trip_bagasian_info', 'Informasi Trip & Bagasian')">Informasi Trip & Bagasian</span>
                                    </h4>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-zinc-100 text-zinc-800 border border-zinc-200" x-text="selectedOrderDetail.trip.code"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                                    <div class="bg-zinc-50 p-2.5 rounded-lg border border-zinc-100">
                                        <span class="text-[10px] text-zinc-400 block font-medium" x-text="t('flight_route', 'Rute Penerbangan')">Rute Penerbangan</span>
                                        <span class="font-bold text-zinc-900" x-text="(selectedOrderDetail.trip.origin_country || 'Jepang') + ' ➔ ' + (selectedOrderDetail.trip.destination_country || 'Indonesia')"></span>
                                    </div>
                                    <div class="bg-zinc-50 p-2.5 rounded-lg border border-zinc-100">
                                        <span class="text-[10px] text-zinc-400 block font-medium" x-text="t('estimated_arrival', 'Estimasi Kedatangan')">Estimasi Kedatangan</span>
                                        <span class="font-bold text-zinc-900" x-text="formatDate(selectedOrderDetail.trip.arrival_at)"></span>
                                    </div>
                                </div>
                                <template x-if="selectedOrderDetail.shipment">
                                    <div class="pt-2 border-t border-zinc-100 flex justify-between items-center text-xs">
                                        <span class="text-zinc-500" x-text="t('estimated_packing_weight', 'Estimasi Berat Packing:')">Estimasi Berat Packing:</span>
                                        <span class="font-bold text-zinc-900 tabular" x-text="(selectedOrderDetail.shipment.packing_estimate_weight || 0) + ' gram'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Card 4: Shipping Address Snapshot -->
                        <template x-if="selectedOrderDetail.address">
                            <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-2 shadow-xs">
                                <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 stroke-current fill-none text-zinc-600" viewBox="0 0 24 24" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span x-text="t('shipping_address_snapshot', 'Alamat Pengiriman (Snapshot)')">Alamat Pengiriman (Snapshot)</span>
                                </h4>
                                <div class="text-xs space-y-1 pt-1">
                                    <p class="font-bold text-zinc-900" x-text="selectedOrderDetail.address.recipient_name + ' (' + selectedOrderDetail.address.phone + ')'"></p>
                                    <p class="text-zinc-600 leading-relaxed" x-text="selectedOrderDetail.address.address"></p>
                                    <template x-if="selectedOrderDetail.address.delivery_note">
                                        <p class="text-[11px] text-zinc-600 bg-zinc-50 p-2 rounded-lg mt-1 border border-zinc-100 font-medium" x-text="(currentLang === 'en' ? 'Delivery Note: ' : 'Catatan Pengantaran: ') + getDeliveryNoteLabel(selectedOrderDetail.address.delivery_note)"></p>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Card 5: Order Items List with OOS (Barang Habis) Simulation -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 stroke-current fill-none text-zinc-600" viewBox="0 0 24 24" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                    <span x-text="t('purchased_products_list', 'Daftar Produk yang Dibeli')">Daftar Produk yang Dibeli</span>
                                </h4>
                                <span class="text-[10px] text-zinc-400 font-medium" x-text="(selectedOrderDetail.items?.length || 0) + (currentLang === 'en' ? ' items' : ' item')"></span>
                            </div>

                            <div class="divide-y divide-zinc-100">
                                <template x-for="item in selectedOrderDetail.items" :key="item.id">
                                    <div class="py-3 space-y-2">
                                        <div class="flex items-center gap-3">
                                            <img :src="getFallbackImage({ name: (item.product_name || item.name) })" class="w-12 h-12 rounded-lg object-cover bg-zinc-100 border border-zinc-200 flex-shrink-0" alt="Item">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <h5 class="text-xs font-bold text-zinc-900" x-text="item.product_name || item.name || 'Produk Pesanan'"></h5>
                                                    <template x-if="item.is_oos && !item.resolution && !item.refund_status && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-red-100 text-red-700 border border-red-200" x-text="t('oos_jp_badge', 'Habis di JP')">Habis di JP</span>
                                                    </template>
                                                    <template x-if="item.resolution === 'refunded' || item.refund_status">
                                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-200" x-text="item.refund_amount ? (currentLang === 'en' ? 'Refunded ' + formatRupiah(item.refund_amount) : 'Dana Direfund ' + formatRupiah(item.refund_amount)) : (currentLang === 'en' ? 'Refunded' : 'Dana Direfund')"></span>
                                                    </template>
                                                    <template x-if="item.resolution === 'replaced' || (item.product_name && item.product_name.includes('(Pengganti'))">
                                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="t('replaced_badge', 'Diganti')">Diganti</span>
                                                    </template>
                                                </div>
                                                <p class="text-[11px] text-zinc-500 tabular" x-text="item.qty + ' x ' + formatRupiah(item.price || item.unit_price || (item.qty ? item.subtotal / item.qty : item.subtotal))"></p>
                                            </div>
                                            <span class="font-bold text-xs text-zinc-900 flex-shrink-0 tabular" x-text="formatRupiah(item.subtotal)"></span>
                                        </div>

                                        <!-- OOS Action Controls -->
                                        <div class="flex items-center justify-between text-xs pt-1 border-t border-dashed border-zinc-100">
                                            <template x-if="!item.is_oos && !item.resolution && !item.refund_status && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                                <button @click="triggerOosSimulation(item)" class="text-[10px] font-semibold text-red-600 hover:text-red-800 flex items-center gap-1 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition min-h-[32px]">
                                                    <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <span x-text="t('sim_mark_oos', '[Simulasi] Tandai Barang Habis di JP')">[Simulasi] Tandai Barang Habis di JP</span>
                                                </button>
                                            </template>
                                            <template x-if="item.is_oos && !item.resolution && !item.refund_status && !(item.product_name && item.product_name.includes('(Pengganti'))">
                                                <button @click="openOosModal(item)" class="text-[10px] font-bold text-white bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-lg transition flex items-center gap-1 shadow-xs min-h-[34px]">
                                                    <span x-text="t('select_solution_btn', 'Pilih Solusi: Refund / Ganti Produk ➔')">Pilih Solusi: Refund / Ganti Produk ➔</span>
                                                </button>
                                            </template>
                                            <template x-if="item.resolution === 'refunded' || (item.refund_status && !(item.product_name && item.product_name.includes('(Pengganti')))">
                                                <div class="space-y-2 w-full">
                                                    <span class="text-[11px] text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200 font-medium block" x-text="(currentLang === 'en' ? 'Refund for item price (' + formatRupiah(item.refund_amount || item.subtotal) + ') has been requested.' : 'Dana seharga barang (' + formatRupiah(item.refund_amount || item.subtotal) + ') telah diajukan refund.')"></span>
                                                    <a 
                                                        :href="getWaRefundUrl(item)" 
                                                        target="_blank" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25D366]/10 hover:bg-[#25D366]/20 border border-[#25D366]/30 text-[#128C7E] hover:text-[#075E54] text-xs font-bold rounded-lg transition min-h-[36px]">
                                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                        <span x-text="t('chat_wa_claim_refund', 'Chat ke WA untuk Klaim Refund') + ' (' + formatRupiah(item.refund_amount || item.subtotal) + ')'"></span>
                                                    </a>
                                                </div>
                                            </template>
                                            <template x-if="item.resolution === 'replaced' || (item.product_name && item.product_name.includes('(Pengganti'))">
                                                <div class="space-y-2 w-full">
                                                    <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-1 rounded border border-emerald-200 font-medium block" x-text="(currentLang === 'en' ? 'Successfully replaced with: ' : 'Berhasil diganti ke: ') + (item.product_name || item.name) + (item.refund_amount ? (currentLang === 'en' ? ' (Difference ' + formatRupiah(item.refund_amount) + ' requested refund)' : ' (Selisih ' + formatRupiah(item.refund_amount) + ' diajukan refund)') : '')"></span>
                                                    <template x-if="item.refund_amount && item.refund_amount > 0">
                                                        <a 
                                                            :href="getWaRefundUrl(item)" 
                                                            target="_blank" 
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25D366]/10 hover:bg-[#25D366]/20 border border-[#25D366]/30 text-[#128C7E] hover:text-[#075E54] text-xs font-bold rounded-lg transition min-h-[36px]">
                                                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                            <span x-text="t('chat_wa_refund_diff', 'Chat ke WA untuk Refund Selisih') + ' (' + formatRupiah(item.refund_amount) + ')'"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Card 5b: Klaim Refund Selisih via WhatsApp (Jika ada barang refund / selisih lebih murah) -->
                        <template x-if="hasRefundItems()">
                            <div class="bg-emerald-50/90 border border-emerald-200 rounded-xl p-4 space-y-3 shadow-xs">
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
                                    class="w-full py-2.5 px-3 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold rounded-lg flex items-center justify-center gap-2 shadow-xs transition min-h-[44px]">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                    <span x-text="t('chat_wa_request_refund_diff', 'Chat ke WA untuk Meminta Refund Selisih') + ' (' + formatRupiah(getTotalRefundAmount()) + ')'"></span>
                                </a>
                            </div>
                        </template>

                        <!-- Card 6: 2-Stage Payment Breakdown -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-2.5 shadow-xs">
                            <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                <svg class="w-4 h-4 stroke-current fill-none text-zinc-600" viewBox="0 0 24 24" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                <span x-text="t('payment_breakdown', 'Rincian Pembayaran')">Rincian Pembayaran</span>
                            </h4>

                            <!-- Stage 1 Breakdown -->
                            <div class="space-y-1.5 text-xs pt-1">
                                <div class="flex justify-between items-center text-zinc-400 font-bold uppercase text-[10px] tracking-wider pb-0.5">
                                    <span x-text="t('product_payment', 'Pembayaran Produk')">Pembayaran Produk</span>
                                </div>
                                <div class="flex justify-between items-center text-zinc-600">
                                    <span x-text="t('product_subtotal', 'Subtotal Produk:')">Subtotal Produk:</span>
                                    <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.product_subtotal || 0)"></span>
                                </div>
                                <template x-if="selectedOrderDetail.pricing?.product_discount_amount > 0">
                                    <div class="flex justify-between items-center text-emerald-600">
                                        <span x-text="t('product_discount', 'Diskon Produk:')">Diskon Produk:</span>
                                        <span class="font-medium tabular" x-text="'- ' + formatRupiah(selectedOrderDetail.pricing.product_discount_amount)"></span>
                                    </div>
                                </template>
                                <template x-if="selectedOrderDetail.pricing?.new_user_discount_amount > 0">
                                    <div class="flex justify-between items-center text-emerald-600">
                                        <span x-text="t('new_user_discount_label', 'Diskon Pengguna Baru:')">Diskon Pengguna Baru:</span>
                                        <span class="font-medium tabular" x-text="'- ' + formatRupiah(selectedOrderDetail.pricing.new_user_discount_amount)"></span>
                                    </div>
                                </template>
                                <template x-if="selectedOrderDetail.pricing?.voucher_amount > 0">
                                    <div class="flex justify-between items-center text-emerald-600">
                                        <span x-text="t('voucher_discount_label', 'Diskon Voucher:')">Diskon Voucher:</span>
                                        <span class="font-medium tabular" x-text="'- ' + formatRupiah(selectedOrderDetail.pricing.voucher_amount)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center text-zinc-600">
                                    <span x-text="t('handling_fee_label', 'Biaya Layanan (Handling Fee):')">Biaya Layanan (Handling Fee):</span>
                                    <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.handling_fee_amount || 0)"></span>
                                </div>
                                <div class="flex justify-between items-center font-bold text-zinc-950 pt-1 border-t border-zinc-100">
                                    <span x-text="t('total_product_payment_label', 'Total Pembayaran Produk:')">Total Pembayaran Produk:</span>
                                    <span class="text-[#E60012] font-bold tabular text-sm" x-text="formatRupiah(selectedOrderDetail.pricing?.product_total || 0)"></span>
                                </div>
                            </div>

                            <!-- Stage 2 Breakdown (Ongkir) -->
                            <div class="space-y-1.5 text-xs pt-3 border-t border-zinc-100">
                                <div class="flex justify-between items-center text-zinc-400 font-bold uppercase text-[10px] tracking-wider pb-0.5">
                                    <span x-text="t('shipping_payment', 'Pembayaran Pengiriman (Ongkir)')">Pembayaran Pengiriman (Ongkir)</span>
                                </div>
                                <template x-if="(selectedOrderDetail.pricing?.shipping_total || 0) === 0">
                                    <p class="text-[11px] text-zinc-500 bg-zinc-50 p-2.5 rounded-lg border border-zinc-100" x-text="t('shipping_breakdown_notice', 'Ongkir dihitung dan ditagihkan terpisah ketika barang telah tiba di Indonesia / siap kirim.')">
                                        Ongkir dihitung dan ditagihkan terpisah ketika barang telah tiba di Indonesia / siap kirim.
                                    </p>
                                </template>
                                <template x-if="(selectedOrderDetail.pricing?.shipping_total || 0) > 0">
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between items-center text-zinc-600">
                                            <span x-text="t('jastip_shipping_label', 'Ongkir Jastip (Jepang ➔ ID):')">Ongkir Jastip (Jepang ➔ ID):</span>
                                            <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.shipping_jastip_amount || 0)"></span>
                                        </div>
                                        <div class="flex justify-between items-center text-zinc-600">
                                            <span x-text="t('domestic_shipping_label', 'Ongkir Lokal (Kurir Domestik):')">Ongkir Lokal (Kurir Domestik):</span>
                                            <span class="font-medium text-zinc-900 tabular" x-text="formatRupiah(selectedOrderDetail.pricing?.shipping_local_amount || 0)"></span>
                                        </div>
                                        <div class="flex justify-between items-center font-bold text-zinc-950 pt-1 border-t border-zinc-100">
                                            <span x-text="t('total_shipping_payment', 'Total Pembayaran Pengiriman:')">Total Pembayaran Pengiriman:</span>
                                            <span class="text-[#E60012] font-bold tabular text-sm" x-text="formatRupiah(selectedOrderDetail.pricing?.shipping_total || 0)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Grand Total -->
                            <div class="flex justify-between items-center pt-3 border-t border-zinc-200 text-sm font-extrabold">
                                <span class="text-zinc-950">Total Keseluruhan (Grand Total):</span>
                                <span class="text-[#E60012] tabular text-base" x-text="formatRupiah(selectedOrderDetail.pricing?.grand_total || (selectedOrderDetail.pricing?.product_total + (selectedOrderDetail.pricing?.shipping_total || 0)))"></span>
                            </div>
                        </div>

                        <!-- Card 7: Distinct Invoices (Produk, Ongkir, dan Tambahan) -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-3 shadow-xs">
                            <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                <svg class="w-4 h-4 stroke-current fill-none text-zinc-600" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <span x-text="t('invoices_title', 'Rincian Invoice Pembayaran')">Rincian Invoice Pembayaran</span>
                            </h4>

                            <div class="space-y-2.5">
                                <!-- 1. Tagihan Produk -->
                                <template x-for="inv in getProductInvoices(selectedOrderDetail)" :key="inv.id">
                                    <div class="p-3 rounded-lg border text-xs space-y-2" :class="inv.status === 'paid' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-zinc-50 border-zinc-200'">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-700 uppercase font-mono tracking-wider" x-text="t('product_invoice', 'Invoice Produk')">Invoice Produk</span>
                                                <h5 class="font-mono font-bold text-zinc-900 mt-1" x-text="inv.invoice_number"></h5>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span 
                                                    :class="inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200'" 
                                                    class="text-[9px] font-bold px-2 py-0.5 rounded border"
                                                    x-text="inv.status === 'paid' ? t('status_paid', 'LUNAS') : t('status_unpaid', 'MENUNGGU PEMBAYARAN')">
                                                </span>
                                                <button 
                                                    @click="downloadInvoicePdf(selectedOrderDetail.id, inv.id)" 
                                                    class="px-2 py-1 bg-white hover:bg-zinc-100 text-zinc-700 border border-zinc-200 rounded-md text-[11px] font-semibold transition flex items-center gap-1 shadow-2xs min-h-[30px]"
                                                    :title="t('download_pdf', 'Unduh PDF')">
                                                    <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    <span x-text="t('download_pdf', 'Unduh PDF')">Unduh PDF</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-1 border-t border-zinc-200/60">
                                            <span class="text-zinc-600" x-text="t('product_amount_label', 'Nominal Produk:')">Nominal Produk:</span>
                                            <span class="font-bold text-zinc-950 tabular text-sm" x-text="formatRupiah(inv.amount)"></span>
                                        </div>
                                        <template x-if="inv.status !== 'paid'">
                                            <button 
                                                @click="simulatePaymentWebhook(inv.invoice_number)" 
                                                class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg transition min-h-[44px]" x-text="t('pay_product_invoice_sim', 'Bayar Invoice Produk Sekarang (Simulasi)')">
                                                Bayar Invoice Produk Sekarang (Simulasi)
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                <!-- 2. Tagihan Tambahan (Jika Ada: Selisih Ganti Produk) -->
                                <template x-for="inv in getAdditionalInvoices(selectedOrderDetail)" :key="inv.id">
                                    <div class="p-3 rounded-lg border text-xs space-y-2" :class="inv.status === 'paid' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/50 border-amber-200'">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase font-mono tracking-wider" :class="inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'" x-text="t('additional_invoice_label', 'Invoice Tambahan (Ganti Produk)')">Invoice Tambahan (Ganti Produk)</span>
                                                <h5 class="font-mono font-bold text-zinc-900 mt-1" x-text="inv.invoice_number"></h5>
                                                <p class="text-[10px] text-zinc-500 mt-0.5" x-text="inv.description"></p>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span 
                                                    :class="inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-200 text-amber-900 border-amber-300'" 
                                                    class="text-[9px] font-bold px-2 py-0.5 rounded border"
                                                    x-text="inv.status === 'paid' ? (inv.description && (inv.description.includes('refund') || inv.description.includes('potong')) ? t('status_offset', 'LUNAS (KOMPENSASI REFUND)') : t('status_paid', 'LUNAS')) : t('status_unpaid', 'MENUNGGU PEMBAYARAN')">
                                                </span>
                                                <button 
                                                    @click="downloadInvoicePdf(selectedOrderDetail.id, inv.id)" 
                                                    class="px-2 py-1 bg-white hover:bg-zinc-100 text-zinc-700 border border-zinc-200 rounded-md text-[11px] font-semibold transition flex items-center gap-1 shadow-2xs min-h-[30px]"
                                                    :title="t('download_pdf', 'Unduh PDF')">
                                                    <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    <span x-text="t('download_pdf', 'Unduh PDF')">Unduh PDF</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-1" :class="inv.status === 'paid' ? 'border-emerald-200/60' : 'border-amber-200/60'">
                                            <span class="text-zinc-600" x-text="t('diff_amount_label', 'Nominal Selisih:')">Nominal Selisih:</span>
                                            <span class="font-bold tabular text-sm" :class="inv.status === 'paid' ? 'text-emerald-700' : 'text-[#E60012]'" x-text="formatRupiah(inv.amount)"></span>
                                        </div>
                                        <template x-if="inv.status !== 'paid'">
                                            <button 
                                                @click="simulatePaymentWebhook(inv.invoice_number)" 
                                                class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg transition min-h-[44px]" x-text="(currentLang === 'en' ? 'Pay Additional Invoice (' : 'Bayar Invoice Tambahan (') + formatRupiah(inv.amount) + ')'">
                                            </button>
                                        </template>
                                        <template x-if="inv.status === 'paid' && inv.description && (inv.description.includes('refund') || inv.description.includes('potong'))">
                                            <div class="p-2 bg-emerald-100/70 text-emerald-800 rounded text-[11px] font-medium flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <span x-text="t('paid_via_refund_offset_notice', 'Lunas otomatis dipotong dari hak saldo refund pesanan ini')">Lunas otomatis dipotong dari hak saldo refund pesanan ini</span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- 3. Tagihan Ongkos Kirim -->
                                <template x-if="getShippingInvoice(selectedOrderDetail)">
                                    <div class="p-3 rounded-lg border text-xs space-y-2" :class="getShippingInvoice(selectedOrderDetail).status === 'paid' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-blue-50/50 border-blue-200'">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 uppercase font-mono tracking-wider" x-text="t('shipping_invoice_badge', 'Invoice Ongkir')">Invoice Ongkir</span>
                                                <h5 class="font-mono font-bold text-zinc-900 mt-1" x-text="getShippingInvoice(selectedOrderDetail).invoice_number"></h5>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span 
                                                    :class="getShippingInvoice(selectedOrderDetail).status === 'paid' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-blue-100 text-blue-800 border-blue-200'" 
                                                    class="text-[9px] font-bold px-2 py-0.5 rounded border"
                                                    x-text="getShippingInvoice(selectedOrderDetail).status === 'paid' ? t('shipping_paid_status', 'ONGKIR LUNAS') : t('shipping_unpaid_status', 'MENUNGGU ONGKIR')">
                                                </span>
                                                <button 
                                                    @click="downloadInvoicePdf(selectedOrderDetail.id, getShippingInvoice(selectedOrderDetail).id)" 
                                                    class="px-2 py-1 bg-white hover:bg-zinc-100 text-zinc-700 border border-zinc-200 rounded-md text-[11px] font-semibold transition flex items-center gap-1 shadow-2xs min-h-[30px]"
                                                    :title="t('download_pdf', 'Unduh PDF')">
                                                    <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                                    <span x-text="t('download_pdf', 'Unduh PDF')">Unduh PDF</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center pt-1 border-t border-zinc-200/60">
                                            <span class="text-zinc-600" x-text="t('shipping_cost_label', 'Nominal Ongkos Kirim:')">Nominal Ongkos Kirim:</span>
                                            <span class="font-bold text-zinc-950 tabular text-sm" x-text="formatRupiah(getShippingInvoice(selectedOrderDetail).amount)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- PDF Download & WhatsApp Actions -->
                            <div class="pt-2 flex flex-wrap gap-2">
                                <template x-if="selectedOrderDetail.invoices && selectedOrderDetail.invoices.length > 0">
                                    <button 
                                        @click="openInvoiceDownloadModal(selectedOrderDetail)" 
                                        class="flex-1 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5 min-h-[44px]">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        <span x-text="selectedOrderDetail.invoices.length > 1 ? t('download_invoice_select_btn', 'Pilih & Unduh Invoice PDF') : t('download_pdf_invoice', 'Unduh Invoice PDF')">Unduh Invoice PDF</span>
                                    </button>
                                </template>
                                <a 
                                    :href="selectedOrderDetail.actions?.whatsapp_contact_url || ('https://wa.me/6281200000001?text=Halo%20Admin%2C%20tanya%20pesanan%20' + selectedOrderDetail.order_number)" 
                                    target="_blank" 
                                    class="flex-1 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5 min-h-[44px]">
                                    <svg class="w-4 h-4 fill-current text-emerald-600" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.082-2.146-.517-1.748-.724-2.883-2.493-2.971-2.609-.088-.116-.713-.949-.713-1.808s.45-1.282.61-1.455c.16-.174.348-.218.464-.218.116 0 .232.002.333.007.106.005.249-.041.389.296.145.348.493 1.204.536 1.292.043.087.072.19.014.305-.058.116-.087.188-.174.29-.087.101-.183.227-.261.305-.088.087-.18.181-.077.357.102.174.455.75 1.047 1.278.761.678 1.403.888 1.602.986.199.098.316.084.433-.051.117-.135.5-.584.633-.787.133-.203.267-.17.449-.102.183.069 1.157.546 1.355.645.199.1.332.149.38.232.049.083.049.48-.095.885z"/></svg>
                                    <span x-text="t('chat_cs_btn', 'Chat CS')">Chat CS</span>
                                </a>
                            </div>

                            <!-- Completed Actions: Review & Reorder -->
                            <template x-if="selectedOrderDetail.status === 'completed'">
                                <div class="pt-2 border-t border-zinc-100 flex gap-2">
                                    <button 
                                        @click="openReviewModal(selectedOrderDetail)" 
                                        class="flex-1 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition min-h-[44px]" x-text="t('review_product_btn', 'Ulas Produk')">
                                        Ulas Produk
                                    </button>
                                    <button 
                                        @click="reorderItems(selectedOrderDetail)" 
                                        class="flex-1 py-2.5 bg-[#E60012] hover:bg-red-700 text-white rounded-lg text-xs font-bold transition min-h-[44px]" x-text="t('buy_again_btn', 'Beli Lagi')">
                                        Beli Lagi
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Card 7B: DEDICATED SHIPPING PAYMENT CARD (KUNCI JIKA PRODUK BELUM LUNAS) -->
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-3 shadow-xs w-full max-w-full overflow-hidden box-border">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-zinc-950 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 stroke-current fill-none text-zinc-700" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                    <span x-text="t('shipping_payment_title', 'Pelunasan Tagihan Ongkos Kirim')">Pelunasan Tagihan Ongkos Kirim</span>
                                </h4>
                                <template x-if="getShippingInvoice(selectedOrderDetail)?.status === 'paid'">
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="t('shipping_paid_status', 'ONGKIR LUNAS')">ONGKIR LUNAS</span>
                                </template>
                            </div>

                            <!-- Kondisi A: Invoice Produk BELUM Lunas (TOMBOL TERKUNCI/DISABLED) -->
                            <template x-if="!isProductInvoicePaid(selectedOrderDetail)">
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg space-y-2">
                                    <button disabled class="w-full py-2.5 bg-zinc-200 text-zinc-400 text-xs font-bold rounded-lg cursor-not-allowed flex items-center justify-center gap-2 min-h-[44px]">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        <span x-text="t('pay_shipping_locked', 'Bayar Ongkos Kirim (Terkunci)')">Bayar Ongkos Kirim (Terkunci)</span>
                                    </button>
                                    <p class="text-[11px] text-zinc-500 text-center font-medium leading-relaxed flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-zinc-400 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        <span x-html="t('shipping_locked_desc')"></span>
                                    </p>
                                </div>
                            </template>

                            <!-- Kondisi B: Invoice Produk SUDAH Lunas & Invoice Ongkir Tersedia (TOMBOL AKTIF) -->
                            <template x-if="isProductInvoicePaid(selectedOrderDetail) && getShippingInvoice(selectedOrderDetail) && getShippingInvoice(selectedOrderDetail).status !== 'paid'">
                                <div class="space-y-2.5">
                                    <div class="flex justify-between items-center text-xs bg-zinc-50 p-2.5 rounded-lg border border-zinc-200">
                                        <span class="text-zinc-600" x-text="t('shipping_bill_amount', 'Nominal Tagihan Ongkir:')">Nominal Tagihan Ongkir:</span>
                                        <span class="font-extrabold text-[#E60012] tabular text-sm" x-text="formatRupiah(getShippingInvoice(selectedOrderDetail).amount)"></span>
                                    </div>
                                    <button 
                                        @click="simulatePaymentWebhook(getShippingInvoice(selectedOrderDetail).invoice_number)" 
                                        class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg transition min-h-[44px] flex items-center justify-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4 stroke-current fill-none text-emerald-400" viewBox="0 0 24 24" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                        <span x-text="(currentLang === 'en' ? 'Pay Shipping Now (' : 'Bayar Ongkos Kirim Sekarang (') + formatRupiah(getShippingInvoice(selectedOrderDetail).amount) + ')'"></span>
                                    </button>
                                    <p class="text-[11px] text-emerald-700 text-center font-medium" x-text="t('shipping_unlocked_desc', '✓ Invoice produk telah lunas. Anda dapat menyelesaikan pembayaran ongkos kirim.')">
                                        ✓ Invoice produk telah lunas. Anda dapat menyelesaikan pembayaran ongkos kirim.
                                    </p>
                                </div>
                            </template>

                            <!-- Kondisi C: Invoice Produk Lunas, Namun Invoice Ongkir Belum Diterbitkan Admin -->
                            <template x-if="isProductInvoicePaid(selectedOrderDetail) && !getShippingInvoice(selectedOrderDetail)">
                                <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg space-y-2 text-center">
                                    <p class="text-xs text-zinc-600 font-medium" x-text="t('shipping_not_issued_desc', 'Invoice produk telah lunas. Invoice ongkos kirim akan diterbitkan otomatis ketika pesanan telah tiba di Indonesia & siap dikirim.')">
                                        Invoice produk telah lunas. Invoice ongkos kirim akan diterbitkan otomatis ketika pesanan telah tiba di Indonesia & siap dikirim.
                                    </p>
                                    <div class="pt-1">
                                        <button @click="adminSimulateStatus(selectedOrderDetail.id, 'ready_for_delivery', 35000)" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-800 text-[11px] font-bold rounded-lg transition max-w-full min-h-[38px]">
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none text-amber-500 shrink-0" viewBox="0 0 24 24" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                            <span class="truncate" x-text="t('admin_sim_issue_shipping', '[Simulasi Admin] Terbitkan Invoice Ongkir Rp 35.000')">[Simulasi Admin] Terbitkan Invoice Ongkir Rp 35.000</span>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Kondisi D: Invoice Ongkir SUDAH Lunas -->
                            <template x-if="getShippingInvoice(selectedOrderDetail)?.status === 'paid'">
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2.5 text-xs text-emerald-800">
                                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="16 10 11 15 8 12"></polyline></svg>
                                    <div>
                                        <p class="font-bold" x-text="t('shipping_paid_success', 'Ongkos Kirim Telah Lunas')">Ongkos Kirim Telah Lunas</p>
                                        <p class="text-[11px] text-emerald-700" x-text="t('shipping_queue_desc', 'Pesanan telah masuk antrian pengantaran kurir ke alamat Anda.')">Pesanan telah masuk antrian pengantaran kurir ke alamat Anda.</p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Card 8: Admin Status Simulator inside Detail Transaksi -->
                        <div class="p-3.5 bg-zinc-100 border border-zinc-200 rounded-xl space-y-2 text-xs w-full max-w-full overflow-hidden box-border">
                            <span class="text-zinc-600 font-bold block" x-text="t('admin_sim_status_title', 'Simulasi Admin (Ubah Status Pesanan):')">Simulasi Admin (Ubah Status Pesanan):</span>
                            <div class="flex gap-2 w-full min-w-0">
                                <select x-model="adminSelectedStatus" class="min-w-0 flex-1 w-full text-xs border border-zinc-300 rounded-lg p-2 bg-white font-medium min-h-[44px] truncate">
                                    <option value="pending_payment_product" x-text="t('opt_pending_payment_product', 'Pending Bayar Produk')">Pending Bayar Produk</option>
                                    <option value="paid_product" x-text="t('opt_paid_product', 'Produk Dibayar (paid_product)')">Produk Dibayar (paid_product)</option>
                                    <option value="processing" x-text="t('opt_processing', 'Diproses (processing)')">Diproses (processing)</option>
                                    <option value="packing" x-text="t('opt_packing', 'Packing (packing)')">Packing (packing)</option>
                                    <option value="ready_for_delivery" x-text="t('opt_ready_for_delivery', 'Siap Kirim / Ongkir')">Siap Kirim / Ongkir</option>
                                    <option value="shipping_paid" x-text="t('opt_shipping_paid', 'Ongkir Dibayar (shipping_paid)')">Ongkir Dibayar (shipping_paid)</option>
                                    <option value="delivering" x-text="t('opt_delivering', 'Sedang Dikirim (delivering)')">Sedang Dikirim (delivering)</option>
                                    <option value="completed" x-text="t('opt_completed', 'Pesanan Selesai (completed)')">Pesanan Selesai (completed)</option>
                                </select>
                                <button @click="changeOrderStatus(selectedOrderDetail.id)" class="shrink-0 px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-bold rounded-lg transition min-h-[44px]" x-text="t('update_btn', 'Update')">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
