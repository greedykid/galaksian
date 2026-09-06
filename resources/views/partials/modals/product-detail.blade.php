<!-- PRODUCT DETAIL BOTTOM SHEET MODAL -->
<div x-show="showProductDetailModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:leave="transition ease-in duration-250"
     x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center overflow-hidden" 
     style="display: none;"
     @keydown.window.escape="closeProductDetailModal()">
    
    <!-- Smooth Backdrop Fade -->
    <div x-show="showProductDetailModal"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-250"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-[2px]" 
         @click="closeProductDetailModal()">
    </div>

    <!-- Sliding Bottom Sheet Panel (Smooth slide up / down) -->
    <div x-show="showProductDetailModal"
         x-transition:enter="transition-transform ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-transform ease-in duration-250 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         :style="getSheetStyle('productDetail')"
         class="relative w-full max-w-[430px] bg-white rounded-t-[28px] max-h-[88vh] overflow-hidden flex flex-col shadow-2xl z-10 border-t border-zinc-100">
        
        <!-- Grab Bar Handle (Swipeable Up/Down when pressed) -->
        <div class="pt-3 pb-1.5 flex flex-col items-center justify-center cursor-grab active:cursor-grabbing select-none touch-none w-full"
             @touchstart.passive="startSheetDrag('productDetail', $event)"
             @mousedown="startSheetDrag('productDetail', $event)">
            <div class="w-12 h-1.5 bg-zinc-300 rounded-full hover:bg-zinc-400 active:bg-zinc-500 transition-colors"></div>
        </div>

        <!-- Modal Header -->
        <div class="px-4 py-2.5 border-b border-zinc-100 flex justify-between items-center bg-white cursor-grab active:cursor-grabbing select-none"
             @touchstart.passive="startSheetDrag('productDetail', $event)"
             @mousedown="startSheetDrag('productDetail', $event)">
            <div class="min-w-0 pr-2">
                <h3 class="font-bold text-xs text-zinc-900 truncate" x-text="activeProduct?.name"></h3>
            </div>
            <button @click.stop="closeProductDetailModal()" 
                    type="button"
                    class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Product Details (Scrollable) -->
        <div class="p-4 space-y-4 overflow-y-auto flex-1">
            <div class="w-full h-60 bg-zinc-50 rounded-2xl overflow-hidden relative border border-zinc-100 shadow-inner">
                <img :src="activeProduct?.primary_image || getFallbackImage(activeProduct)" 
                     class="w-full h-full object-cover" 
                     onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                <span :class="activeProduct?.availability_type === 'ready_stock' ? 'bg-[#00D06C] text-white' : 'bg-zinc-950 text-white'"
                      class="absolute top-2.5 right-2.5 text-[9px] font-bold px-2 py-0.5 rounded-full font-mono shadow-xs">
                    <span x-text="activeProduct?.availability_type === 'ready_stock' ? t('ready_badge', 'READY STOCK') : t('po_badge', 'PRE-ORDER (PO)')"></span>
                </span>
            </div>

            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-xl font-extrabold text-zinc-950 tabular" x-text="formatRupiah(activeProduct?.final_price || 0)"></span>
                    <template x-if="activeProduct?.has_discount">
                        <span class="text-xs text-zinc-400 line-through tabular" x-text="formatRupiah(activeProduct?.price || 0)"></span>
                    </template>
                </div>
                <h2 class="text-sm font-bold text-zinc-900 mt-1" x-text="activeProduct?.name"></h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[11px] text-zinc-500 font-medium" x-text="'Brand: ' + (activeProduct?.brand?.name || 'Jepang')"></span>
                    <span class="text-zinc-300">•</span>
                    <span class="text-[11px] text-zinc-500" x-text="'SKU: ' + (activeProduct?.sku || '-')"></span>
                </div>
            </div>

            <div class="p-3.5 bg-zinc-50 rounded-2xl text-xs space-y-1 text-zinc-600 border border-zinc-100">
                <span class="font-bold text-zinc-900 block" x-text="t('product_desc_label', 'Keterangan Produk:')">Keterangan Produk:</span>
                <p class="leading-relaxed" x-text="activeProduct?.description || 'Produk orisinal langsung dari toko resmi di Tokyo. Dikemas rapi dengan proteksi anti-pecah.'"></p>
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky bottom-0 bg-white border-t border-zinc-100 p-4 flex items-center justify-between gap-3 shadow-[0_-4px_12px_rgba(0,0,0,0.03)]">
            <div class="flex items-center gap-1 bg-zinc-100 border border-zinc-200 rounded-xl p-1">
                <button @click="detailModalQty = Math.max(1, detailModalQty - 1)" 
                        type="button"
                        class="w-8 h-8 bg-white rounded-lg text-zinc-800 font-bold hover:bg-zinc-50 flex items-center justify-center shadow-xs text-sm">
                    -
                </button>
                <span class="text-xs font-bold px-2.5 tabular text-zinc-900" x-text="detailModalQty"></span>
                <button @click="detailModalQty++" 
                        type="button"
                        class="w-8 h-8 bg-[#1657FF] text-white rounded-lg font-bold hover:bg-blue-700 flex items-center justify-center shadow-xs text-sm">
                    +
                </button>
            </div>

            <button @click="addToCart(activeProduct, detailModalQty); closeProductDetailModal()" 
                    type="button"
                    class="flex-1 py-3 bg-[#1657FF] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-blue-500/20 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span x-text="t('add_to_cart_btn', 'Tambah ke Keranjang')">Tambah ke Keranjang</span>
            </button>
        </div>
    </div>
</div>
