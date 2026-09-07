<!-- ========================================================= -->
<!-- SUB-VIEW: CHECKOUT (REFERENCE DESIGN IMPLEMENTATION)      -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'checkout'" class="bg-white min-h-screen">
    
    <!-- 1. Dedicated Top Header (Matching uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center gap-2">
            <button @click="closeSubView()" class="p-1 -ml-1 text-white hover:text-white/80 transition flex items-center gap-1.5 cursor-pointer" :title="t('back', 'Kembali')">
                <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('checkout_title', 'Checkout')">Checkout</h1>
        </div>
        <span class="px-3 py-1 bg-white/20 text-white font-bold text-xs rounded-full tabular shadow-2xs" x-text="(cart.total_qty || 0) + ' ' + t('items', 'item')"></span>
    </div>

    <!-- Main Content Container with Soft, Modern Border Cards -->
    <div class="p-4 space-y-3 pb-28">
        
        <!-- CARD 1: TUJUAN PENGIRIMAN -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 space-y-3 shadow-2xs">
            <h3 class="font-extrabold text-xs text-zinc-950" x-text="t('shipping_destination', 'Tujuan Pengiriman')">Tujuan Pengiriman</h3>
            
            <!-- Warning Notice Banner (Khusus gedung, hanya bisa diantar sampai lobby) -->
            <div class="bg-blue-50 border border-blue-200/80 rounded-xl p-2.5 flex items-center gap-2.5 text-[#1657FF] text-xs font-medium">
                <svg class="w-4 h-4 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                    <line x1="9" y1="6" x2="9.01" y2="6"></line>
                    <line x1="15" y1="6" x2="15.01" y2="6"></line>
                    <line x1="9" y1="10" x2="9.01" y2="10"></line>
                    <line x1="15" y1="10" x2="15.01" y2="10"></line>
                    <line x1="9" y1="14" x2="9.01" y2="14"></line>
                    <line x1="15" y1="14" x2="15.01" y2="14"></line>
                    <line x1="9" y1="18" x2="15" y2="18"></line>
                </svg>
                <span class="leading-tight" x-text="t('building_lobby_note', 'Khusus gedung, hanya bisa diantar sampai lobby')">Khusus gedung, hanya bisa diantar sampai lobby</span>
            </div>

            <!-- Address Information Row -->
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <!-- Blue House Icon in Square -->
                    <div class="w-10 h-10 rounded-xl bg-[#1657FF] text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-zinc-900 line-clamp-1" x-text="defaultAddress ? ('Home, ' + defaultAddress.recipient_name) : t('no_address', 'Belum Ada Alamat')"></h4>
                        <p class="text-[11px] text-zinc-500 line-clamp-2 mt-0.5 leading-snug" x-text="defaultAddress ? (defaultAddress.address + ', ' + (defaultAddress.city || '') + (defaultAddress.postal_code ? ', ' + defaultAddress.postal_code : '')) : t('please_add_or_select_address', 'Silakan pilih atau tambahkan alamat baru.')"></p>
                    </div>
                </div>
                <!-- Rounded Edit Pencil Button -->
                <button @click="openAddressPicker()" class="w-8 h-8 rounded-xl border border-zinc-200 hover:border-zinc-400 bg-zinc-50 flex items-center justify-center text-zinc-600 hover:text-zinc-900 transition shrink-0 cursor-pointer" :title="t('change_address', 'Ubah Alamat')">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- CARD 2: PENGIRIMAN JASTIP EKSPEDISI -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-zinc-900" x-text="t('jastip_expedition_shipping', 'Pengiriman Jastip Ekspedisi')">Pengiriman Jastip Ekspedisi</h4>
                    <p class="text-[11px] text-zinc-500" x-text="t('shipping_working_days', '3–5 Hari Kerja')">3–5 Hari Kerja</p>
                </div>
            </div>
            <button @click="showToast('Jadwal pengiriman otomatis diselaraskan dengan trip aktif.')" class="font-bold text-xs text-[#1657FF] hover:underline" x-text="t('schedule_btn', 'Jadwalkan')">
                Jadwalkan
            </button>
        </div>

        <!-- CARD 3: DAFTAR PRODUK -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 space-y-3 shadow-2xs">
            <div class="flex justify-between items-center">
                <h3 class="font-extrabold text-xs text-zinc-950" x-text="t('ordered_products', 'Daftar Produk')">Daftar Produk</h3>
                <span class="text-xs text-zinc-400 tabular" x-text="(cart.total_qty || 0) + ' ' + t('items', 'item')"></span>
            </div>

            <div class="space-y-3">
                <template x-for="item in cart.items" :key="item.id">
                    <div class="flex items-center justify-between gap-3 py-1 border-b border-zinc-100 last:border-b-0">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <!-- Thumbnail with Percentage Discount Badge -->
                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-zinc-50 relative shrink-0 border border-zinc-100">
                                <img :src="item.product.primary_image || getFallbackImage(item.product)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                <span class="absolute top-1 left-1 bg-[#00D06C] text-white text-[9px] font-black px-1.5 py-0.2 rounded-full shadow-2xs" x-text="'-' + (item.discount_percentage || (item.product?.discount_price ? Math.round((1 - (item.product.discount_price/item.product.price))*100) : 40)) + '%'"></span>
                            </div>

                            <!-- Title, Price, Strikethrough, and Savings Badge -->
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-xs text-zinc-900 line-clamp-1" x-text="item.product.name"></h4>
                                <div class="flex items-baseline gap-1.5 mt-0.5">
                                    <span class="text-xs font-bold text-zinc-950 tabular" x-text="formatRupiah(item.unit_price || item.discount_price || item.price)"></span>
                                    <span class="text-[10px] text-zinc-400 line-through tabular" x-text="formatRupiah(item.price || item.product?.price || ((item.unit_price || item.price) * 1.5))"></span>
                                </div>
                                <div class="mt-0.5">
                                    <span class="inline-block bg-emerald-50 text-[#00A862] text-[10px] font-bold px-2 py-0.2 rounded-full border border-emerald-200/50" 
                                          x-text="t('save_prefix', 'Hemat ') + formatRupiah(((item.price || item.product?.price || 0) - (item.unit_price || item.discount_price || item.price)) > 0 ? ((item.price || item.product?.price || 0) - (item.unit_price || item.discount_price || item.price)) : 135000)">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Blue Capsule Stepper [-] qty [+] -->
                        <div class="flex items-center bg-[#1657FF] text-white rounded-full px-1.5 py-0.5 shadow-xs shrink-0">
                            <button @click="updateCartItemQty(item.id, item.qty - 1)" class="w-5 h-5 flex items-center justify-center font-bold text-xs hover:bg-blue-700 rounded-full transition active:scale-90">-</button>
                            <span class="px-1.5 text-xs font-black tabular" x-text="item.qty"></span>
                            <button @click="updateCartItemQty(item.id, item.qty + 1)" class="w-5 h-5 flex items-center justify-center font-bold text-xs hover:bg-blue-700 rounded-full transition active:scale-90">+</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CARD 4: PROMO & VOUCHER JASTIP -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 space-y-3 shadow-2xs">
            <div class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#1657FF] stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <h3 class="font-extrabold text-xs text-zinc-950" x-text="t('promo_voucher_title', 'Promo & Voucher Jastip')">Promo & Voucher Jastip</h3>
            </div>

            <!-- Voucher Option 1 (GALAKSIAN10) -->
            <div class="p-3 rounded-xl border border-zinc-200/80 bg-zinc-50/50 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5">
                            <line x1="19" y1="5" x2="5" y2="19"></line>
                            <circle cx="6.5" cy="6.5" r="2.5"></circle>
                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-zinc-900">Diskon Jastip 10% (GALAKSIAN10)</h4>
                        <p class="text-[10px] text-zinc-500">Min. belanja Rp200rb · Semua produk</p>
                    </div>
                </div>
                <button 
                    @click="handleVoucherAction('GALAKSIAN10')" 
                    :class="getVoucherState('GALAKSIAN10') === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState('GALAKSIAN10') === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700')"
                    class="px-3.5 py-1.5 font-extrabold text-xs rounded-xl shadow-2xs transition active:scale-95 shrink-0 cursor-pointer">
                    <span x-text="getVoucherState('GALAKSIAN10') === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState('GALAKSIAN10') === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim'))"></span>
                </button>
            </div>

            <!-- Voucher Option 2 (NEWUSER15) -->
            <div class="p-3 rounded-xl border border-zinc-200/80 bg-zinc-50/50 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1657FF] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                            <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                            <path d="M12 8v13"></path>
                            <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-zinc-900">Cashback Jastip Rp15.000 (NEWUSER15)</h4>
                        <p class="text-[10px] text-zinc-500">Berlaku untuk pengguna baru</p>
                    </div>
                </div>
                <button 
                    @click="handleVoucherAction('NEWUSER15')" 
                    :class="getVoucherState('NEWUSER15') === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState('NEWUSER15') === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700')"
                    class="px-3.5 py-1.5 font-extrabold text-xs rounded-xl shadow-2xs transition active:scale-95 shrink-0 cursor-pointer">
                    <span x-text="getVoucherState('NEWUSER15') === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState('NEWUSER15') === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim'))"></span>
                </button>
            </div>

            <!-- Promo Code Input & Apply Button -->
            <div class="flex items-center gap-2">
                <input 
                    type="text" 
                    x-model="voucherCode" 
                    :placeholder="t('promo_code_placeholder', 'Kode promo...')" 
                    class="flex-1 px-3.5 py-2 rounded-xl border border-zinc-200 bg-white text-xs placeholder-zinc-400 focus:outline-none focus:ring-1 focus:ring-blue-600 font-mono uppercase"
                >
                <button 
                    @click="applyVoucher()" 
                    class="px-4 py-2 bg-[#1657FF] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-2xs cursor-pointer">
                    <span x-text="t('use_btn', 'Pakai')">Pakai</span>
                </button>
            </div>

            <!-- Clickable Helper Chips -->
            <div class="text-[10px] text-zinc-400 flex items-center gap-1.5 flex-wrap">
                <span x-text="t('try_claim', 'Coba klaim:')">Coba klaim:</span>
                <button @click="handleVoucherAction('GALAKSIAN10')" class="font-mono text-zinc-600 hover:text-[#1657FF] hover:underline cursor-pointer">GALAKSIAN10</button>
                <span>·</span>
                <button @click="handleVoucherAction('HEMAT5')" class="font-mono text-zinc-600 hover:text-[#1657FF] hover:underline cursor-pointer">HEMAT5</button>
                <span>·</span>
                <button @click="handleVoucherAction('NEWUSER15')" class="font-mono text-zinc-600 hover:text-[#1657FF] hover:underline cursor-pointer">NEWUSER15</button>
            </div>
        </div>

        <!-- OPTIONAL CARD: BINGKISAN & GREETING CARD SUMMARY (If active) -->
        <template x-if="giftOptionEnabled">
            <div class="bg-blue-50/70 border border-blue-200/90 rounded-2xl p-3.5 flex items-start gap-3 shadow-2xs">
                <div class="w-9 h-9 rounded-xl bg-[#1657FF] text-amber-300 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                        <path d="M12 8v13"></path>
                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0 text-xs">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-blue-950" x-text="t('send_as_gift', 'Kirim sebagai Bingkisan')">Kirim sebagai Bingkisan</h4>
                        <span class="text-[10px] font-extrabold text-[#1657FF] bg-blue-100 px-2 py-0.2 rounded-full">+Rp 10.000</span>
                    </div>
                    <p class="text-[11px] text-zinc-600 mt-0.5" x-text="giftCardTo ? (t('for_prefix', 'Untuk: ') + giftCardTo + (giftCardFrom ? (' · ' + t('from_prefix', 'Dari: ') + giftCardFrom) : '')) : t('gift_desc_short', 'Kemasan khusus & kartu ucapan')"></p>
                    <template x-if="giftCardMessage">
                        <p class="text-[10px] text-zinc-500 italic mt-1 bg-white/80 p-1.5 rounded-lg border border-blue-100" x-text="'&ldquo;' + giftCardMessage + '&rdquo;'"></p>
                    </template>
                </div>
            </div>
        </template>

        <!-- CARD 5: METODE PEMBAYARAN -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 space-y-2.5 shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#1657FF] stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="21" x2="21" y2="21"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <polyline points="5 6 12 3 19 6"></polyline>
                    <line x1="4" y1="10" x2="4" y2="21"></line>
                    <line x1="20" y1="10" x2="20" y2="21"></line>
                </svg>
                <h3 class="font-extrabold text-xs text-zinc-950" x-text="t('payment_method', 'Metode Pembayaran')">Metode Pembayaran</h3>
            </div>

            <div class="space-y-2">
                <!-- Option 1: Virtual Account (Selected in Reference) -->
                <div 
                    @click="checkoutForm.payment_method = 'virtual_account'" 
                    :class="checkoutForm.payment_method === 'virtual_account' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'" 
                    class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1657FF] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                                <line x1="3" y1="21" x2="21" y2="21"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                <polyline points="5 6 12 3 19 6"></polyline>
                                <line x1="4" y1="10" x2="4" y2="21"></line>
                                <line x1="20" y1="10" x2="20" y2="21"></line>
                            </svg>
                        </div>
                        <div>
                            <h4 :class="checkoutForm.payment_method === 'virtual_account' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs">Virtual Account</h4>
                            <p class="text-[10px] text-zinc-500">BCA · Mandiri · BNI · BRI</p>
                        </div>
                    </div>
                    <!-- Custom Radio -->
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="checkoutForm.payment_method === 'virtual_account' ? 'border-[#1657FF]' : 'border-zinc-300'">
                        <template x-if="checkoutForm.payment_method === 'virtual_account'">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                        </template>
                    </div>
                </div>

                <!-- Option 2: PayPal -->
                <div 
                    @click="checkoutForm.payment_method = 'paypal'" 
                    :class="checkoutForm.payment_method === 'paypal' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'" 
                    class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center font-black text-sm shrink-0">
                            P
                        </div>
                        <div>
                            <h4 :class="checkoutForm.payment_method === 'paypal' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs">PayPal</h4>
                            <p class="text-[10px] text-zinc-500" x-text="t('pay_with_paypal', 'Bayar dengan akun PayPal')">Bayar dengan akun PayPal</p>
                        </div>
                    </div>
                    <!-- Custom Radio -->
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="checkoutForm.payment_method === 'paypal' ? 'border-[#1657FF]' : 'border-zinc-300'">
                        <template x-if="checkoutForm.payment_method === 'paypal'">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                        </template>
                    </div>
                </div>

                <!-- Option 3: QRIS -->
                <div 
                    @click="checkoutForm.payment_method = 'qris'" 
                    :class="checkoutForm.payment_method === 'qris' ? 'border-2 border-[#1657FF] bg-blue-50/30 shadow-2xs' : 'border border-zinc-200 hover:bg-zinc-50/50'" 
                    class="rounded-2xl p-3 flex items-center justify-between cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </div>
                        <div>
                            <h4 :class="checkoutForm.payment_method === 'qris' ? 'text-[#1657FF]' : 'text-zinc-900'" class="font-bold text-xs">QRIS</h4>
                            <p class="text-[10px] text-zinc-500">GoPay · OVO · Dana · LinkAja</p>
                        </div>
                    </div>
                    <!-- Custom Radio -->
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition" :class="checkoutForm.payment_method === 'qris' ? 'border-[#1657FF]' : 'border-zinc-300'">
                        <template x-if="checkoutForm.payment_method === 'qris'">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#1657FF]"></div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 6: RINCIAN PEMBAYARAN -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3.5 space-y-2 text-xs shadow-2xs">
            <h3 class="font-extrabold text-xs text-zinc-950 mb-1" x-text="t('payment_breakdown_title', 'Rincian Pembayaran')">Rincian Pembayaran</h3>

            <!-- Total Harga Barang -->
            <div class="flex justify-between items-center text-zinc-600">
                <span x-text="t('total_goods_price', 'Total Harga Barang') + ' (' + (cart.total_qty || 0) + ' ' + t('items', 'Item') + ')'"></span>
                <div class="flex items-center gap-1.5 text-right">
                    <template x-if="cart.pricing?.promo_discount > 0">
                        <span class="text-[11px] text-zinc-400 line-through tabular" x-text="formatRupiah(cart.pricing?.raw_subtotal || cart.pricing?.subtotal || 0)"></span>
                    </template>
                    <span class="font-bold text-zinc-900 tabular" x-text="formatRupiah(cart.pricing?.discounted_subtotal || (cart.pricing?.subtotal - (cart.pricing?.promo_discount || 0)))"></span>
                    <template x-if="cart.pricing?.promo_discount > 0">
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100/80 px-1.5 py-0.2 rounded-full tabular" x-text="t('save_prefix', 'Hemat ') + formatRupiah(cart.pricing.promo_discount)"></span>
                    </template>
                </div>
            </div>

            <!-- Diskon Pengguna Baru -->
            <template x-if="cart.pricing?.new_user_discount > 0">
                <div class="flex justify-between items-center text-[#00D06C]">
                    <span x-text="t('new_user_discount_label', 'Diskon Pengguna Baru')">Diskon Pengguna Baru</span>
                    <span class="font-bold tabular" x-text="'- ' + formatRupiah(cart.pricing.new_user_discount)"></span>
                </div>
            </template>

            <!-- Diskon Voucher (If any) -->
            <template x-if="cart.pricing?.voucher_discount > 0">
                <div class="flex justify-between items-center text-[#00D06C]">
                    <span x-text="t('voucher_discount', 'Diskon Voucher')">Diskon Voucher</span>
                    <span class="font-bold tabular" x-text="'- ' + formatRupiah(cart.pricing.voucher_discount)"></span>
                </div>
            </template>

            <!-- Biaya Jastip & Handling -->
            <div class="flex justify-between items-center text-zinc-600">
                <span x-text="t('jastip_handling_fee', 'Biaya Jastip & Handling')">Biaya Jastip & Handling</span>
                <span class="font-bold text-zinc-900 tabular" x-text="formatRupiah(cart.pricing?.handling_fee || 5000)"></span>
            </div>

            <!-- Biaya Bingkisan & Kartu Ucapan (If enabled) -->
            <template x-if="giftOptionEnabled || (cart.pricing?.gift_fee > 0)">
                <div class="flex justify-between items-center text-zinc-600">
                    <div>
                        <span class="block font-medium text-zinc-800" x-text="t('gift_card_fee_label', 'Biaya Bingkisan & Kartu')">Biaya Bingkisan & Kartu</span>
                        <span class="text-[10px] text-zinc-400 block" x-text="giftCardTo ? (t('for_prefix', 'Untuk: ') + giftCardTo) : t('gift_desc_short', 'Kemasan khusus & ucapan')"></span>
                    </div>
                    <span class="font-bold text-zinc-900 tabular" x-text="'+ ' + formatRupiah(cart.pricing?.gift_fee || 10000)"></span>
                </div>
            </template>

            <!-- Asuransi Pengiriman (Ditagihkan bersama ongkir tahap 2) -->
            <div class="flex justify-between items-center text-zinc-600">
                <span x-text="t('shipping_insurance', 'Asuransi Pengiriman')">Asuransi Pengiriman</span>
                <span class="font-bold text-zinc-900 tabular" x-text="isInsuranceChecked ? ('Rp 2.000 ' + t('paid_with_shipping', '(Ditagihkan bersama ongkir)')) : 'Rp 0'"></span>
            </div>

            <!-- Ongkos Kirim Ekspedisi -->
            <div class="flex justify-between items-center text-zinc-600">
                <span x-text="t('expedition_shipping_cost', 'Ongkos Kirim Ekspedisi')">Ongkos Kirim Ekspedisi</span>
                <span class="italic text-zinc-400" x-text="t('calculating', 'Menghitung...')">Menghitung...</span>
            </div>

            <!-- Divider Line -->
            <div class="border-t border-zinc-100 pt-2 mt-2 flex justify-between items-baseline">
                <span class="font-extrabold text-xs text-zinc-900" x-text="t('total_bill', 'Total Tagihan')">Total Tagihan</span>
                <span class="text-base font-black text-[#1657FF] tabular" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
            </div>

            <span class="text-[10px] text-zinc-400 block" x-text="t('shipping_excluded_notice_checkout', '*Belum termasuk ongkos kirim')">*Belum termasuk ongkos kirim</span>
        </div>

        <!-- CARD 7: ASURANSI PENGIRIMAN TOGGLE -->
        <div class="bg-white rounded-2xl border border-zinc-200/90 p-3 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-xs text-zinc-900" x-text="t('shipping_insurance', 'Asuransi Pengiriman')">Asuransi Pengiriman</h4>
                    <p class="text-[10px] text-zinc-500" x-text="t('insurance_desc', 'Proteksi kehilangan & kerusakan · Rp 2.000')">Proteksi kehilangan & kerusakan · Rp 2.000</p>
                </div>
            </div>

            <!-- Green Toggle Switch -->
            <button 
                type="button" 
                @click="isInsuranceChecked = !isInsuranceChecked" 
                :class="isInsuranceChecked ? 'bg-[#00D06C]' : 'bg-zinc-300'" 
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                role="switch" 
                :aria-checked="isInsuranceChecked">
                <span 
                    :class="isInsuranceChecked ? 'translate-x-5' : 'translate-x-0'" 
                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow-sm transform ring-0 transition duration-200 ease-in-out">
                </span>
            </button>
        </div>

    </div>
</div>
