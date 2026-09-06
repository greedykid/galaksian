<!-- ========================================================= -->
            <!-- VIEW 2: KERANJANG (CART)                                  -->
            <!-- ========================================================= -->
            <div x-show="activeTab === 'cart' && !activeSubView" class="space-y-4">
                <!-- Header Cart -->
                <div class="px-4 pt-3 pb-2 border-b border-zinc-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-950 tracking-tight" x-text="t('shopping_cart', 'Keranjang Belanja')">Keranjang Belanja</h2>
                        <p class="text-[11px] text-zinc-500"><span class="font-bold tabular" x-text="cart.total_qty || 0"></span> <span x-text="t('items_saved', 'item tersimpan')">item tersimpan</span></p>
                    </div>
                    <button @click="fetchCart()" class="text-xs font-semibold text-zinc-600 hover:text-zinc-950">
                        Refresh
                    </button>
                </div>

                <!-- Recipient Address Banner -->
                <div class="mx-4 bg-zinc-50 border border-zinc-200 rounded-xl p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-md bg-zinc-200 text-zinc-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-zinc-900" x-text="t('shipping_address', 'Alamat Pengiriman')">Alamat Pengiriman</span>
                                    <template x-if="defaultAddress">
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-zinc-200 text-zinc-800" x-text="t('primary_address_badge', 'Utama')">Utama</span>
                                    </template>
                                </div>
                                <template x-if="defaultAddress">
                                    <div class="text-[11px] text-zinc-600 mt-0.5">
                                        <p class="font-semibold text-zinc-800" x-text="defaultAddress.recipient_name + ' • ' + defaultAddress.phone"></p>
                                        <p class="line-clamp-2 text-zinc-500" x-text="defaultAddress.address + ', ' + (defaultAddress.city || '')"></p>
                                    </div>
                                </template>
                                <template x-if="!defaultAddress">
                                    <p class="text-[11px] text-zinc-500 mt-0.5" x-text="t('no_shipping_address', 'Belum ada alamat pengiriman. Silakan tambah alamat.')">Belum ada alamat pengiriman. Silakan tambah alamat.</p>
                                </template>
                            </div>
                        </div>
                        <button @click="goToTab('profile'); profileTab = 'alamat'" class="text-xs font-semibold text-[#E60012] hover:underline flex-shrink-0" x-text="t('edit_btn', 'Ubah')">
                            Ubah
                        </button>
                    </div>
                </div>

                <!-- Empty Cart -->
                <template x-if="!cart.items || cart.items.length === 0">
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        </div>
                        <h4 class="font-bold text-sm text-zinc-900" x-text="t('empty_cart_title', 'Keranjang Belanja Kosong')">Keranjang Belanja Kosong</h4>
                        <p class="text-xs text-zinc-500" x-text="t('empty_cart_desc', 'Pilih produk dari katalog Jepang dan Indonesia untuk memulai titip belanja.')">Pilih produk dari katalog Jepang dan Indonesia untuk memulai titip belanja.</p>
                        <button @click="goToTab('home')" class="px-4 py-2 bg-zinc-950 text-white font-semibold text-xs rounded-xl hover:bg-zinc-800 transition" x-text="t('start_shopping_btn', 'Mulai Belanja')">
                            Mulai Belanja
                        </button>
                    </div>
                </template>

                <!-- Cart Items List -->
                <template x-if="cart.items && cart.items.length > 0">
                    <div class="px-4 space-y-3">
                        <template x-for="item in cart.items" :key="item.id">
                            <div class="bg-white border border-zinc-200 rounded-xl p-3 flex gap-3">
                                <!-- Thumbnail -->
                                <div class="w-16 h-16 rounded-lg bg-zinc-50 flex-shrink-0 overflow-hidden border border-zinc-100 relative">
                                    <img :src="item.product.primary_image || getFallbackImage(item.product)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                    <!-- Discount Badge on thumbnail -->
                                    <template x-if="item.has_discount || (item.discount_price && item.discount_price < item.price)">
                                        <span class="absolute top-1 left-1 bg-[#00D06C] text-white text-[8px] font-black px-1.5 py-0.2 rounded-full shadow-2xs">
                                            <span x-text="'-' + (item.discount_percentage || Math.round((1 - ((item.unit_price || item.discount_price) / item.price)) * 100)) + '%'"></span>
                                        </span>
                                    </template>
                                </div>
                                <!-- Details -->
                                <div class="flex-1 flex flex-col justify-between">
                                    <div class="flex justify-between items-start">
                                        <div class="pr-2">
                                            <span 
                                                :class="item.product.availability_type === 'ready_stock' ? 'text-emerald-700' : 'text-zinc-600'"
                                                class="text-[9px] font-bold uppercase tracking-wider block" 
                                                x-text="item.product.availability_type === 'ready_stock' ? 'Ready Stock' : 'Open PO'">
                                            </span>
                                            <h4 class="text-xs font-semibold text-zinc-900 line-clamp-1" x-text="item.product.name"></h4>
                                            
                                            <!-- Price & Discount Display -->
                                            <div class="mt-0.5 flex items-baseline gap-1.5 flex-wrap">
                                                <!-- Current Unit Price (After Discount) -->
                                                <span class="text-xs font-extrabold text-zinc-950 tabular" 
                                                      x-text="formatRupiah(item.unit_price || item.product?.final_price || item.discount_price || item.price)">
                                                </span>
                                                
                                                <!-- Original Strikethrough & Percentage Pill if Discounted -->
                                                <template x-if="item.has_discount || (item.discount_price && item.discount_price < item.price)">
                                                    <div class="inline-flex items-center gap-1">
                                                        <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(item.price || item.product?.price)"></span>
                                                        <span class="bg-[#00D06C]/15 text-[#00A862] text-[9px] font-bold px-1.5 py-0.2 rounded font-mono" 
                                                              x-text="'-' + (item.discount_percentage || Math.round((1 - ((item.unit_price || item.discount_price) / item.price)) * 100)) + '%'">
                                                        </span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <!-- Delete -->
                                        <button @click="removeCartItem(item.id)" class="text-zinc-400 hover:text-red-600 p-1">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                        </button>
                                    </div>

                                    <!-- Stepper -->
                                    <div class="flex justify-between items-center mt-2 pt-1 border-t border-zinc-100">
                                        <span class="text-[10px] text-zinc-400">Subtotal: <span class="font-semibold text-zinc-800 tabular" x-text="formatRupiah(item.subtotal)"></span></span>
                                        <div class="flex items-center gap-1 bg-zinc-50 border border-zinc-200 rounded-lg p-0.5">
                                            <button @click="updateCartItemQty(item.id, item.qty - 1)" class="w-5 h-5 bg-white border border-zinc-200 rounded text-xs font-bold text-zinc-700 flex items-center justify-center hover:bg-zinc-100">-</button>
                                            <span class="text-xs font-bold px-1.5 text-zinc-900 tabular" x-text="item.qty"></span>
                                            <button @click="updateCartItemQty(item.id, item.qty + 1)" class="w-5 h-5 bg-[#1657FF] hover:bg-blue-700 rounded text-xs font-bold text-white flex items-center justify-center transition-colors">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Voucher Code Input -->
                        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3 space-y-2">
                            <label class="block text-xs font-bold text-zinc-800" x-text="t('coupon_label', 'Kupon / Voucher Promo')">Kupon / Voucher Promo</label>
                            <div class="flex gap-2">
                                <input 
                                    type="text" 
                                    x-model="voucherCode" 
                                    :placeholder="t('coupon_placeholder', 'Contoh: GALAKSIAN10 / POTONGAN15K')" 
                                    class="flex-1 px-3 py-1.5 bg-white border border-zinc-200 rounded-lg text-xs font-mono uppercase focus:outline-none focus:ring-1 focus:ring-zinc-900"
                                >
                                <button @click="applyVoucher()" class="px-3.5 py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs rounded-lg transition" x-text="t('apply_btn', 'Terapkan')">
                                    Terapkan
                                </button>
                            </div>
                            <template x-if="cart.voucher_applied">
                                <div class="flex items-center justify-between text-xs bg-emerald-50 text-emerald-800 border border-emerald-200 px-2.5 py-1 rounded-md font-medium">
                                    <span><span x-text="t('active_voucher', 'Voucher aktif:')">Voucher aktif:</span> <strong class="font-mono" x-text="cart.voucher_applied.code"></strong></span>
                                    <span class="font-bold tabular" x-text="'- ' + formatRupiah(cart.pricing.voucher_discount)"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Price Breakdown (RINGKASAN BIAYA) -->
                        <!-- CRITICAL DOMAIN RULE: TOTAL ONGKIR DITIADAKAN/DIHAPUS PADA KERANJANG! -->
                        <div class="bg-zinc-50 border border-zinc-200 rounded-xl p-3.5 space-y-2.5 text-xs">
                            <h4 class="font-bold text-zinc-950" x-text="t('order_summary', 'Rincian Pembayaran Produk')">Rincian Pembayaran Produk</h4>
                            
                            <div class="flex justify-between text-zinc-600">
                                <span x-text="t('product_subtotal', 'Subtotal Produk')">Subtotal Produk</span>
                                <span class="font-semibold text-zinc-900 tabular" x-text="formatRupiah(cart.pricing?.subtotal || 0)"></span>
                            </div>

                            <template x-if="cart.pricing?.promo_discount > 0">
                                <div class="flex justify-between text-[#E60012] font-medium">
                                    <span x-text="t('promo_discount', 'Diskon Promo Produk')">Diskon Promo Produk</span>
                                    <span class="tabular" x-text="'- ' + formatRupiah(cart.pricing.promo_discount)"></span>
                                </div>
                            </template>

                            <template x-if="cart.pricing?.voucher_discount > 0">
                                <div class="flex justify-between text-[#E60012] font-medium">
                                    <span x-text="t('voucher_discount', 'Diskon Voucher')">Diskon Voucher</span>
                                    <span class="tabular" x-text="'- ' + formatRupiah(cart.pricing.voucher_discount)"></span>
                                </div>
                            </template>

                            <template x-if="cart.pricing?.new_user_discount > 0">
                                <div class="flex justify-between text-[#E60012] font-medium">
                                    <span x-text="t('new_user_discount', 'Diskon Pengguna Baru')">Diskon Pengguna Baru</span>
                                    <span class="tabular" x-text="'- ' + formatRupiah(cart.pricing.new_user_discount)"></span>
                                </div>
                            </template>

                            <div class="flex justify-between text-zinc-600">
                                <span x-text="t('handling_fee', 'Biaya Penanganan (Handling Fee)')">Biaya Penanganan (Handling Fee)</span>
                                <span class="font-semibold text-zinc-900 tabular" x-text="formatRupiah(cart.pricing?.handling_fee || 5000)"></span>
                            </div>

                            <!-- Explicit Ongkir Notice -->
                            <div class="p-2.5 bg-zinc-100/90 text-zinc-700 rounded-lg text-[11px] border border-zinc-200 leading-relaxed">
                                <span x-html="t('ongkir_separate_notice')"></span>
                            </div>

                            <div class="border-t border-zinc-200 pt-2.5 flex justify-between font-extrabold text-sm text-zinc-950">
                                <span x-text="t('total_product_bill', 'Total Tagihan Produk')">Total Tagihan Produk</span>
                                <span class="text-[#E60012] tabular text-base" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
                            </div>
                        </div>

                        <!-- Spacing for Sticky Action Bar above Bottom Nav -->
                        <div class="pb-24"></div>
                    </div>
                </template>
            </div>
