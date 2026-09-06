<!-- ========================================================= -->
            <!-- SUB-VIEW: CHECKOUT (CONFIRMATION & PAYMENT METHOD)        -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'checkout'" class="space-y-4">
                <!-- Sub-header -->
                <div class="bg-zinc-50 px-4 py-3 border-b border-zinc-200 flex items-center gap-3">
                    <button @click="closeSubView()" class="w-8 h-8 rounded-lg bg-white border border-zinc-200 text-zinc-700 flex items-center justify-center text-xs hover:bg-zinc-100 transition">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <div>
                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold" x-text="t('checkout_breadcrumb', 'Checkout')">Checkout</span>
                        <h2 class="text-sm font-bold text-zinc-900" x-text="t('order_checkout', 'Konfirmasi & Pembayaran')">Konfirmasi & Pembayaran</h2>
                    </div>
                </div>

                <div class="px-4 space-y-4">
                    <!-- Address Selection Card -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2.5">
                        <div class="flex justify-between items-center">
                            <h3 class="font-bold text-xs text-zinc-950" x-text="t('shipping_address', 'Alamat Pengiriman')">Alamat Pengiriman</h3>
                            <button @click="openAddressModal()" class="text-xs font-semibold text-[#E60012] hover:underline" x-text="t('add_new_btn', '+ Tambah Baru')">
                                + Tambah Baru
                            </button>
                        </div>
                        <template x-if="userAddresses.length === 0">
                            <div class="p-3 bg-zinc-50 rounded-lg text-zinc-600 text-xs border border-zinc-200">
                                <span x-text="t('no_saved_addresses_hint')">Belum ada alamat tersimpan. Klik "+ Tambah Baru".</span>
                            </div>
                        </template>
                        <template x-if="userAddresses.length > 0">
                            <div class="space-y-2">
                                <template x-for="addr in userAddresses" :key="addr.id">
                                    <label class="flex items-start gap-2.5 p-2.5 rounded-lg border cursor-pointer transition" :class="checkoutForm.address_id === addr.id ? 'border-zinc-950 bg-zinc-50' : 'border-zinc-200 hover:bg-zinc-50/50'">
                                        <input type="radio" name="checkout_address" :value="addr.id" x-model="checkoutForm.address_id" class="mt-0.5 text-zinc-950 focus:ring-zinc-950">
                                        <div class="flex-1 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-zinc-900" x-text="addr.recipient_name"></span>
                                                <span class="text-zinc-500" x-text="'(' + addr.phone + ')'"></span>
                                                <template x-if="addr.is_default">
                                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-zinc-200 text-zinc-800" x-text="t('primary_address_badge', 'Utama')">Utama</span>
                                                </template>
                                            </div>
                                            <p class="text-zinc-600 line-clamp-2 mt-0.5" x-text="addr.address + ', ' + (addr.city || '')"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Items Summary Card -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                        <h3 class="font-bold text-xs text-zinc-950" x-text="t('ordered_products', 'Produk yang Dipesan')">Produk yang Dipesan</h3>
                        <div class="space-y-2 max-h-44 overflow-y-auto pr-1">
                            <template x-for="item in cart.items" :key="item.id">
                                <div class="flex justify-between items-center text-xs py-1.5 border-b border-zinc-100 last:border-none">
                                    <div class="flex-1 pr-2">
                                        <p class="font-medium text-zinc-800 line-clamp-1" x-text="item.product?.name || item.product_name || item.name || 'Produk'"></p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[11px] text-zinc-500 tabular" x-text="item.qty + ' x ' + formatRupiah(item.unit_price || item.product?.final_price || item.discount_price || item.price || (item.qty ? item.subtotal / item.qty : 0))"></span>
                                            <template x-if="item.has_discount || (item.discount_price && item.discount_price < item.price)">
                                                <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(item.price || item.product?.price)"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-zinc-900 tabular" x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-1.5">
                        <label class="block text-xs font-bold text-zinc-950" x-text="t('order_notes', 'Catatan untuk Jastiper (Opsional)')">Catatan untuk Jastiper (Opsional)</label>
                        <textarea 
                            x-model="checkoutForm.notes" 
                            rows="2" 
                            :placeholder="t('order_notes_placeholder', 'Instruksi khusus, packing, atau preferensi varian...')" 
                            class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:bg-white focus:ring-1 focus:ring-zinc-900"
                        ></textarea>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                        <h3 class="font-bold text-xs text-zinc-950" x-text="t('payment_method', 'Metode Pembayaran')">Metode Pembayaran</h3>
                        <div class="space-y-2">
                            <!-- QRIS -->
                            <label class="flex items-center justify-between p-2.5 rounded-lg border cursor-pointer transition" :class="checkoutForm.payment_method === 'qris' ? 'border-zinc-950 bg-zinc-50' : 'border-zinc-200 hover:bg-zinc-50/50'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="payment_method" value="qris" x-model="checkoutForm.payment_method" class="text-zinc-950">
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-900" x-text="t('pay_method_qris', 'QRIS (Semua E-Wallet & Mobile Banking)')">QRIS (Semua E-Wallet & Mobile Banking)</span>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_qris_desc', 'GoPay, BCA, OVO, ShopeePay, Dana, Mandiri')">GoPay, BCA, OVO, ShopeePay, Dana, Mandiri</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded" x-text="t('badge_instant', 'Instan')">Instan</span>
                            </label>

                            <!-- Virtual Account -->
                            <label class="flex items-center justify-between p-2.5 rounded-lg border cursor-pointer transition" :class="checkoutForm.payment_method === 'virtual_account' ? 'border-zinc-950 bg-zinc-50' : 'border-zinc-200 hover:bg-zinc-50/50'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="payment_method" value="virtual_account" x-model="checkoutForm.payment_method" class="text-zinc-950">
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-900" x-text="t('pay_method_va', 'Virtual Account Bank')">Virtual Account Bank</span>
                                        <p class="text-[10px] text-zinc-500">BCA, Mandiri, BRI, BNI</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded" x-text="t('badge_auto', 'Otomatis')">Otomatis</span>
                            </label>

                            <!-- PayPal -->
                            <label class="flex items-center justify-between p-2.5 rounded-lg border cursor-pointer transition" :class="checkoutForm.payment_method === 'paypal' ? 'border-zinc-950 bg-zinc-50' : 'border-zinc-200 hover:bg-zinc-50/50'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="payment_method" value="paypal" x-model="checkoutForm.payment_method" class="text-zinc-950">
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-900" x-text="t('pay_method_paypal', 'PayPal / International Card')">PayPal / International Card</span>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_paypal_desc', 'Mata Uang JPY, USD, IDR')">Mata Uang JPY, USD, IDR</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded" x-text="t('badge_global', 'Global')">Global</span>
                            </label>

                            <!-- Manual Transfer -->
                            <label class="flex items-center justify-between p-2.5 rounded-lg border cursor-pointer transition" :class="checkoutForm.payment_method === 'manual' ? 'border-zinc-950 bg-zinc-50' : 'border-zinc-200 hover:bg-zinc-50/50'">
                                <div class="flex items-center gap-2.5">
                                    <input type="radio" name="payment_method" value="manual" x-model="checkoutForm.payment_method" class="text-zinc-950">
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-900" x-text="t('pay_method_manual', 'Transfer Manual Bank')">Transfer Manual Bank</span>
                                        <p class="text-[10px] text-zinc-500" x-text="t('pay_method_manual_desc', 'Konfirmasi bukti transfer ke CS')">Konfirmasi bukti transfer ke CS</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-700 bg-zinc-100 px-2 py-0.5 rounded" x-text="t('badge_manual', 'Manual')">Manual</span>
                            </label>
                        </div>
                    </div>

                    <!-- Total & Spacing for Sticky Action Bar -->
                    <div class="pt-2 pb-24 space-y-3">
                        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 flex justify-between items-center text-xs">
                            <span class="font-medium text-zinc-600" x-text="t('total_product_payment_label', 'Total Pembayaran Produk:')">Total Pembayaran Produk:</span>
                            <span class="text-base font-extrabold text-[#E60012] tabular" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
                        </div>
                    </div>
                </div>
            </div>
