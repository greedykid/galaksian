<!-- PRODUCT DETAIL MODAL -->
        <div x-show="activeProduct" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="activeProduct = null">
            <div class="w-full max-w-[430px] bg-white rounded-t-2xl sm:rounded-2xl max-h-[88vh] overflow-y-auto flex flex-col shadow-xl">
                <!-- Modal Top -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm px-4 py-3 border-b border-zinc-100 flex justify-between items-center">
                    <h3 class="font-bold text-xs text-zinc-900 line-clamp-1" x-text="activeProduct?.name"></h3>
                    <button @click="activeProduct = null" class="w-7 h-7 rounded-lg bg-zinc-100 text-zinc-600 flex items-center justify-center text-xs hover:bg-zinc-200">
                        ✕
                    </button>
                </div>

                <!-- Product Details -->
                <div class="p-4 space-y-3">
                    <div class="w-full h-60 bg-zinc-50 rounded-xl overflow-hidden relative border border-zinc-100">
                        <img :src="activeProduct?.primary_image || getFallbackImage(activeProduct)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                        <span 
                            :class="activeProduct?.availability_type === 'ready_stock' ? 'bg-emerald-700 text-white' : 'bg-zinc-900 text-white'"
                            class="absolute top-2.5 right-2.5 text-[9px] font-bold px-2 py-0.5 rounded font-mono">
                            <span x-text="activeProduct?.availability_type === 'ready_stock' ? t('ready_badge', 'READY STOCK') : t('po_badge', 'PRE-ORDER (PO)')"></span>
                        </span>
                    </div>

                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-extrabold text-zinc-950 tabular" x-text="formatRupiah(activeProduct?.final_price || 0)"></span>
                            <template x-if="activeProduct?.has_discount">
                                <span class="text-xs text-zinc-400 line-through tabular" x-text="formatRupiah(activeProduct?.price || 0)"></span>
                            </template>
                        </div>
                        <h2 class="text-sm font-bold text-zinc-900 mt-1" x-text="activeProduct?.name"></h2>
                        <span class="text-xs text-zinc-400 mt-0.5 block" x-text="'Brand: ' + (activeProduct?.brand?.name || 'Jepang')"></span>
                    </div>

                    <div class="p-3 bg-zinc-50 rounded-xl text-xs space-y-1 text-zinc-600 border border-zinc-100">
                        <span class="font-bold text-zinc-900 block" x-text="t('product_desc_label', 'Keterangan Produk:')">Keterangan Produk:</span>
                        <p class="leading-relaxed" x-text="activeProduct?.description || 'Produk orisinal langsung dari toko resmi di Tokyo. Dikemas rapi dengan proteksi anti-pecah.'"></p>
                    </div>
                </div>

                <!-- Sticky Action Bar -->
                <div class="sticky bottom-0 bg-white border-t border-zinc-200 p-4 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 bg-zinc-100 border border-zinc-200 rounded-lg p-0.5">
                        <button @click="detailModalQty = Math.max(1, detailModalQty - 1)" class="w-7 h-7 bg-white rounded text-zinc-800 font-bold">-</button>
                        <span class="text-xs font-bold px-2 tabular" x-text="detailModalQty"></span>
                        <button @click="detailModalQty++" class="w-7 h-7 bg-zinc-950 text-white rounded font-bold">+</button>
                    </div>

                    <button 
                        @click="addToCart(activeProduct, detailModalQty); activeProduct = null" 
                        class="flex-1 py-3 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-xl transition" x-text="t('add_to_cart_btn', 'Tambah ke Keranjang')">
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </div>
