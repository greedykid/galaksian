<!-- ========================================================= -->
<!-- VIEW 2: KERANJANG (CART - REFERENCE DESIGN IMPLEMENTATION) -->
<!-- ========================================================= -->
<div x-show="activeTab === 'cart' && !activeSubView" class="bg-white min-h-screen">
    
    <!-- 1. Dedicated Top Header (Matching uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <button @click="goToTab('home')" class="p-1 -ml-1 text-white hover:text-white/80 transition flex items-center gap-2">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('cart_title', 'Keranjang')">Keranjang</h1>
        </button>
        <span class="bg-[#00D06C] text-white font-bold text-xs px-2.5 py-0.5 rounded-full shadow-2xs tabular" x-text="(cart.total_qty || 0) + ' ' + t('items', 'item')"></span>
    </div>

    <!-- 2. Alamat Pengiriman Card (Seamless Edge-to-edge) -->
    <div class="bg-white border-b border-zinc-100 px-4 py-3 flex items-center justify-between gap-3 -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Blue Pin Icon in Circle -->
            <div class="w-9 h-9 rounded-full bg-blue-50 text-[#1657FF] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
            <!-- Address Content -->
            <div class="min-w-0">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block leading-tight" x-text="t('shipping_address_label', 'ALAMAT PENGIRIMAN')">ALAMAT PENGIRIMAN</span>
                <p class="font-bold text-xs text-zinc-900 line-clamp-1 mt-0.5" x-text="defaultAddress ? defaultAddress.address : t('no_address_saved', 'Belum ada alamat tersimpan')"></p>
                <p class="text-[11px] text-zinc-500 line-clamp-1" x-text="defaultAddress ? ((defaultAddress.city || '') + (defaultAddress.postal_code ? ' ' + defaultAddress.postal_code : '') + (defaultAddress.province ? ', ' + defaultAddress.province : '')) : t('please_set_destination', 'Silakan tentukan alamat tujuan')"></p>
            </div>
        </div>
        <button @click="openAddressModal()" class="px-3.5 py-1 rounded-lg border border-blue-200 text-[#1657FF] hover:bg-blue-50 text-xs font-semibold transition shrink-0 cursor-pointer" x-text="t('edit_btn', 'Ubah')">
            Ubah
        </button>
    </div>

    <!-- 3. Kirim Sebagai Bingkisan Card Banner with Expandable Custom Greeting Card -->
    <div class="mx-4 my-1 rounded-2xl border border-blue-200 bg-blue-50/40 p-3.5 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Gift SVG Icon -->
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#1657FF] to-blue-500 text-amber-300 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                        <path d="M12 8v13"></path>
                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-bold text-xs text-zinc-900" x-text="t('send_as_gift', 'Kirim sebagai Bingkisan')">Kirim sebagai Bingkisan</h4>
                        <span class="text-[10px] font-extrabold text-[#1657FF] bg-blue-100/90 px-1.5 py-0.2 rounded">+Rp 10.000</span>
                    </div>
                    <p class="text-[11px] text-zinc-500" x-text="t('gift_desc', 'Kemasan eksklusif & kartu ucapan custom')">Kemasan eksklusif & kartu ucapan custom</p>
                </div>
            </div>
            <button 
                @click="toggleGiftOption()" 
                :class="giftOptionEnabled ? 'bg-[#00D06C] text-white' : 'bg-[#1657FF] text-white hover:bg-blue-700'"
                class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 transition shadow-2xs cursor-pointer"
                :title="giftOptionEnabled ? t('gift_active', 'Bingkisan Aktif') : t('activate_gift', 'Aktifkan Bingkisan')">
                <template x-if="!giftOptionEnabled">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </template>
                <template x-if="giftOptionEnabled">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </template>
            </button>
        </div>

        <!-- Custom Greeting Card Section (Expands when enabled) -->
        <div x-show="giftOptionEnabled" x-transition class="pt-2.5 border-t border-blue-200/80 space-y-2.5" x-cloak>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-950 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#1657FF] stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span x-text="t('custom_greeting_card_title', 'Kustomisasi Kartu Ucapan')">Kustomisasi Kartu Ucapan</span>
                </span>
                <span class="text-[10px] text-zinc-400 font-medium" x-text="t('printed_on_gift_card', 'Tercetak di kartu bingkisan')">Tercetak di kartu bingkisan</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1" x-text="t('from_sender', 'Dari (Pengirim)')">Dari (Pengirim)</label>
                    <input 
                        type="text" 
                        x-model="giftCardFrom" 
                        @input.debounce.400ms="syncGiftDetails()"
                        :placeholder="t('sender_name_placeholder', 'Nama Pengirim')" 
                        class="w-full px-3 py-1.5 bg-white border border-blue-200 rounded-xl text-xs text-zinc-900 focus:outline-none focus:ring-1 focus:ring-[#1657FF]">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1" x-text="t('to_recipient', 'Untuk (Penerima)')">Untuk (Penerima)</label>
                    <input 
                        type="text" 
                        x-model="giftCardTo" 
                        @input.debounce.400ms="syncGiftDetails()"
                        :placeholder="t('recipient_name_placeholder', 'Nama Penerima')" 
                        class="w-full px-3 py-1.5 bg-white border border-blue-200 rounded-xl text-xs text-zinc-900 focus:outline-none focus:ring-1 focus:ring-[#1657FF]">
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1" x-text="t('greeting_message_label', 'Pesan Ucapan')">Pesan Ucapan</label>
                <textarea 
                    x-model="giftCardMessage" 
                    @input.debounce.400ms="syncGiftDetails()"
                    rows="2" 
                    :placeholder="t('gift_message_placeholder', 'Tulis pesan ucapan Anda (misal: Selamat ulang tahun, semoga suka oleh-oleh dari Jepangnya!)...')" 
                    class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl text-xs text-zinc-900 focus:outline-none focus:ring-1 focus:ring-[#1657FF]"></textarea>
            </div>
        </div>
    </div>

    <!-- 4. Empty Cart State -->
    <template x-if="!cart.items || cart.items.length === 0">
        <div class="p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                    <path d="M3 6h18"></path>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <h4 class="font-bold text-sm text-zinc-900" x-text="t('empty_cart_title', 'Keranjang Belanja Kosong')">Keranjang Belanja Kosong</h4>
            <p class="text-xs text-zinc-500" x-text="t('empty_cart_desc', 'Pilih produk dari katalog Jepang dan Indonesia untuk memulai titip belanja.')">Pilih produk dari katalog Jepang dan Indonesia untuk memulai titip belanja.</p>
            <button @click="goToTab('home')" class="px-5 py-2.5 bg-[#1657FF] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-xs" x-text="t('start_shopping_btn', 'Mulai Belanja')">
                Mulai Belanja
            </button>
        </div>
    </template>

    <!-- 5. Cart Items List (Matching media_1788725780938.png) -->
    <template x-if="cart.items && cart.items.length > 0">
        <div class="space-y-1">
            <template x-for="item in cart.items" :key="item.id">
                <div class="mx-4 py-3.5 border-b border-zinc-100 space-y-2.5">
                    <!-- Product Row -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <!-- Thumbnail with Discount Badge -->
                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-zinc-50 relative shrink-0 border border-zinc-100">
                                <img :src="item.product.primary_image || getFallbackImage(item.product)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                <template x-if="item.has_discount || (item.discount_price && item.discount_price < item.price)">
                                    <span class="absolute top-1 left-1 bg-[#00D06C] text-white text-[8px] font-black px-1.5 py-0.2 rounded-full shadow-2xs">
                                        <span x-text="'-' + (item.discount_percentage || Math.round((1 - ((item.unit_price || item.discount_price) / item.price)) * 100)) + '%'"></span>
                                    </span>
                                </template>
                            </div>
                            <!-- Title & Green Bold Price -->
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-xs text-zinc-900 line-clamp-1" x-text="item.product.name"></h4>
                                <div class="mt-1">
                                    <span class="text-sm font-bold text-[#00D06C] tabular" 
                                          x-text="formatRupiah(item.unit_price || item.product?.final_price || item.discount_price || item.price)">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Round Green Steppers (-) qty (+) -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button 
                                @click="updateCartItemQty(item.id, item.qty - 1)" 
                                class="w-6 h-6 rounded-full bg-[#00D06C] hover:bg-[#00B85F] active:scale-95 text-white font-bold text-xs flex items-center justify-center transition shadow-2xs">
                                -
                            </button>
                            <span class="text-xs font-bold text-zinc-900 px-1 tabular" x-text="item.qty"></span>
                            <button 
                                @click="updateCartItemQty(item.id, item.qty + 1)" 
                                class="w-6 h-6 rounded-full bg-[#00D06C] hover:bg-[#00B85F] active:scale-95 text-white font-bold text-xs flex items-center justify-center transition shadow-2xs">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Dashed Capsule: Tambah Catatan Button -->
                    <div x-show="editingNoteItemId !== item.id">
                        <button 
                            @click="openItemNote(item)" 
                            class="w-full py-2 px-3 border border-dashed border-zinc-300 rounded-xl flex items-center justify-center gap-1.5 text-zinc-500 hover:text-zinc-800 hover:border-zinc-400 bg-white hover:bg-zinc-50/50 transition text-xs font-medium cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                            </svg>
                            <span x-text="itemNotes[item.id] ? (t('note_prefix', 'Catatan: ') + itemNotes[item.id]) : t('add_note_btn', 'Tambah Catatan')">Tambah Catatan</span>
                        </button>
                    </div>

                    <!-- Inline Note Editor (When editing) -->
                    <div x-show="editingNoteItemId === item.id" class="p-2.5 bg-zinc-50 border border-zinc-200 rounded-xl space-y-2" x-cloak>
                        <textarea 
                            x-model="tempNoteText" 
                            rows="2" 
                            :placeholder="t('item_note_placeholder', 'Tulis catatan varian, kemasan, atau instruksi jastip...')"
                            class="w-full p-2 bg-white border border-zinc-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-[#1657FF]"></textarea>
                        <div class="flex justify-end gap-2 text-xs">
                            <button @click="cancelItemNote()" class="px-3 py-1 rounded-lg text-zinc-600 hover:bg-zinc-200 font-medium cursor-pointer" x-text="t('cancel_btn', 'Batal')">Batal</button>
                            <button @click="saveItemNote(item.id)" class="px-3.5 py-1 bg-[#1657FF] text-white font-bold rounded-lg hover:bg-blue-700 cursor-pointer" x-text="t('save_btn', 'Simpan')">Simpan</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- 6. Promo & Voucher Jastip Section (Matching media_1788725780938.png & media_1788725795534.png) -->
    <template x-if="cart.items && cart.items.length > 0">
        <div class="pt-2">
            <!-- Section Header -->
            <div class="px-4 flex items-center justify-between mb-2.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-1 h-3.5 bg-[#1657FF] rounded-full inline-block"></span>
                    <h3 class="font-extrabold text-xs text-zinc-900 tracking-tight" x-text="t('promo_voucher_title', 'Promo & Voucher Jastip')">Promo & Voucher Jastip</h3>
                </div>
                <span class="bg-[#00D06C] text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs" x-text="cart.voucher_applied ? t('voucher_1_applied', '1 Dipakai') : (Object.values(voucherStates).filter(s => s === 'claimed').length ? Object.values(voucherStates).filter(s => s === 'claimed').length + ' ' + t('ready_to_use', 'Siap Pakai') : t('available', 'Tersedia'))">Tersedia</span>
            </div>

            <!-- Card 1: Diskon Jastip 20% + Bebas Ongkir (Yellow Border Card) -->
            <div class="mx-4 rounded-2xl border-2 border-amber-400 bg-amber-50/15 p-3.5 space-y-2.5 shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="5" x2="5" y2="19"></line>
                            <circle cx="6.5" cy="6.5" r="2.5"></circle>
                            <circle cx="17.5" cy="17.5" r="2.5"></circle>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-zinc-900">Diskon Jastip 20% + Bebas Ongkir</h4>
                        <p class="text-[10px] text-zinc-500">Min. belanja Rp200rb · Berlaku s/d besok</p>
                    </div>
                </div>
                <div class="border-t border-dashed border-amber-300 pt-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-[10px] text-emerald-700 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00D06C]"></span>
                        <span x-text="getVoucherState('GALAKSIAN10') === 'applied' ? t('voucher_active_applied', 'Voucher aktif terpasang') : (getVoucherState('GALAKSIAN10') === 'claimed' ? t('ready_to_use_title', 'Siap digunakan') : t('available', 'Tersedia'))"></span>
                    </div>
                    <button 
                        @click="handleVoucherAction('GALAKSIAN10')" 
                        :class="getVoucherState('GALAKSIAN10') === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState('GALAKSIAN10') === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700')"
                        class="px-4 py-1.5 font-extrabold text-xs rounded-lg shadow-2xs transition active:scale-95 cursor-pointer">
                        <span x-text="getVoucherState('GALAKSIAN10') === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState('GALAKSIAN10') === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim'))"></span>
                    </button>
                </div>
            </div>

            <!-- Card 2: Cashback Jastip Rp15.000 (Light Blue Border Card) -->
            <div class="mx-4 mt-2.5 rounded-2xl border border-blue-200 bg-blue-50/20 p-3.5 space-y-2.5 shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#1657FF] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                            <path d="M12 8v13"></path>
                            <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                            <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-zinc-900">Cashback Jastip Rp15.000 (POTONGAN15K)</h4>
                        <p class="text-[10px] text-zinc-500">Min. belanja Rp150rb</p>
                    </div>
                </div>
                <div class="border-t border-dashed border-blue-200 pt-2 flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-[10px] text-zinc-400">
                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                        <span x-text="getVoucherState('POTONGAN15K') === 'applied' ? t('voucher_active_applied', 'Voucher aktif terpasang') : (getVoucherState('POTONGAN15K') === 'claimed' ? t('ready_to_use_title', 'Siap digunakan') : t('not_used', 'Belum dipakai'))"></span>
                    </div>
                    <button 
                        @click="handleVoucherAction('POTONGAN15K')" 
                        :class="getVoucherState('POTONGAN15K') === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState('POTONGAN15K') === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700')"
                        class="px-4 py-1.5 font-extrabold text-xs rounded-lg shadow-2xs transition active:scale-95 cursor-pointer">
                        <span x-text="getVoucherState('POTONGAN15K') === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState('POTONGAN15K') === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim'))"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- 7. Ringkasan Pesanan Section (Matching media_1788725795534.png) -->
    <template x-if="cart.items && cart.items.length > 0">
        <div class="pt-3 pb-24">
            <!-- Section Header -->
            <div class="px-4 flex items-center gap-1.5 mb-2.5">
                <span class="w-1 h-3.5 bg-[#1657FF] rounded-full inline-block"></span>
                <h3 class="font-extrabold text-xs text-zinc-900 tracking-tight" x-text="t('order_summary', 'Ringkasan Pesanan')">Ringkasan Pesanan</h3>
            </div>

            <!-- Breakdown Card -->
            <div class="mx-4 bg-white border border-zinc-100 rounded-2xl p-4 space-y-2 text-xs">
                <!-- Total Harga Barang -->
                <div class="flex justify-between items-center text-zinc-600">
                    <span x-text="t('total_goods_price', 'Total Harga Barang')">Total Harga Barang</span>
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

                <!-- Diskon Pengguna Baru (if any) -->
                <template x-if="cart.pricing?.new_user_discount > 0">
                    <div class="flex justify-between items-center text-[#00D06C]">
                        <span x-text="t('new_user_discount_label', 'Diskon Pengguna Baru:')">Diskon Pengguna Baru:</span>
                        <span class="font-bold tabular" x-text="'- ' + formatRupiah(cart.pricing.new_user_discount)"></span>
                    </div>
                </template>

                <!-- Active Voucher Discount (if any) -->
                <template x-if="cart.pricing?.voucher_discount > 0">
                    <div class="flex justify-between items-center text-[#00D06C]">
                        <span x-text="t('voucher_discount', 'Diskon Voucher')">Diskon Voucher</span>
                        <span class="font-bold tabular" x-text="'- ' + formatRupiah(cart.pricing.voucher_discount)"></span>
                    </div>
                </template>

                <!-- Total Biaya Layanan -->
                <div class="flex justify-between items-start text-zinc-600">
                    <div>
                        <span class="block" x-text="t('total_service_fee', 'Total Biaya Layanan')">Total Biaya Layanan</span>
                        <span class="text-[10px] text-zinc-400 block" x-text="t('service_fee_sub', 'Termasuk biaya jastip & handling')">Termasuk biaya jastip & handling</span>
                    </div>
                    <span class="font-bold text-zinc-900 tabular" x-text="formatRupiah(cart.pricing?.handling_fee || 5000)"></span>
                </div>

                <!-- Biaya Bingkisan & Kartu Ucapan (if enabled) -->
                <template x-if="giftOptionEnabled || (cart.pricing?.gift_fee > 0)">
                    <div class="flex justify-between items-start text-zinc-600">
                        <div>
                            <span class="block font-medium text-zinc-800" x-text="t('gift_card_fee_label', 'Biaya Bingkisan & Kartu')">Biaya Bingkisan & Kartu</span>
                            <span class="text-[10px] text-zinc-400 block" x-text="t('gift_card_fee_sub', 'Kemasan khusus & ucapan')">Kemasan khusus & ucapan</span>
                        </div>
                        <span class="font-bold text-zinc-900 tabular" x-text="'+ ' + formatRupiah(cart.pricing?.gift_fee || 10000)"></span>
                    </div>
                </template>

                <!-- Thin Divider Line -->
                <div class="border-t border-zinc-100 pt-2.5 mt-2 flex justify-between items-baseline">
                    <span class="font-extrabold text-sm text-zinc-900">Total</span>
                    <span class="text-base font-extrabold text-[#00D06C] tabular" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
                </div>

                <!-- Asterisk Ongkir Notice -->
                <span class="text-[10px] text-zinc-400 block pt-0.5" x-text="t('shipping_excluded_notice', '* Belum termasuk biaya pengiriman')">* Belum termasuk biaya pengiriman</span>
            </div>
        </div>
    </template>

</div>
