<template x-if="activeView === 'products'">
    <div class="px-4 py-4 space-y-3">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('product_management', 'Manajemen Produk')"></h1>
            <button @click="openProductForm(null); activeView='product-form'" class="bg-zinc-900 text-white text-xs font-bold px-3 py-2 rounded-xl hover:bg-zinc-800 transition" x-text="t('add_product', '+ Produk')"></button>
        </div>

        <!-- Search -->
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" x-model="productSearch" @input.debounce.400ms="searchProducts()"
                   class="w-full bg-zinc-100 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"
                   :placeholder="t('search_product_placeholder', 'Cari nama atau SKU...')">
        </div>

        <!-- List -->
        <div class="space-y-2">
            <template x-if="productsLoading">
                <div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></div>
            </template>
            <template x-for="p in products" :key="p.id">
                <div class="bg-white border border-zinc-200 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 overflow-hidden">
                        <img x-show="p.primary_image" :src="p.primary_image" class="w-full h-full object-cover" alt="">
                        <span x-show="!p.primary_image" class="text-lg">📦</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-zinc-900 truncate" x-text="p.name"></p>
                        <p class="text-[10px] text-zinc-500" x-text="(p.sku || '-') + ' · ' + (p.brand?.name || '')"></p>
                        <p class="text-xs font-bold text-zinc-900 tabular mt-0.5" x-text="formatRupiah(p.final_price || p.price || 0)"></p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full"
                              :class="p.is_active ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500'"
                              x-text="p.is_active ? t('active','Aktif') : t('inactive','Nonaktif')"></span>
                        <p class="text-[10px] text-zinc-400 mt-1" x-text="'Stok: ' + (p.stock ?? 0)"></p>
                        <div class="flex gap-1 justify-end mt-1">
                            <button @click="openProductForm(p.id); activeView='product-form'" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <button @click="deleteProduct(p.id)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-red-50 flex items-center justify-center text-zinc-500 hover:text-red-600 transition" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="products.length === 0 && !productsLoading">
                <p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_products_found', 'Tidak ada produk')"></p>
            </template>
        </div>

        <!-- Pagination -->
        <template x-if="productsMeta.last_page > 1">
            <div class="flex items-center justify-center gap-2 pt-2">
                <button @click="productsPage--; loadProducts()" :disabled="productsPage <= 1" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">←</button>
                <span class="text-xs font-semibold text-zinc-500" x-text="productsPage + ' / ' + productsMeta.last_page"></span>
                <button @click="productsPage++; loadProducts()" :disabled="productsPage >= productsMeta.last_page" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">→</button>
            </div>
        </template>

        <!-- Import -->
        <button @click="activeView='product-import'" class="w-full bg-zinc-100 text-zinc-700 text-xs font-bold py-3 rounded-xl hover:bg-zinc-200 transition" x-text="t('import_products', 'Import Produk Massal')"></button>
    </div>
</template>
