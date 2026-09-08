<template x-if="activeView === 'product-import'">
    <div class="px-4 py-4 space-y-4">
        <button @click="goTo('products')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_products', 'Kembali ke Produk')"></span>
        </button>
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('import_products', 'Import Produk Massal')"></h1>
        <p class="text-xs text-zinc-500">Format JSON: <code class="font-mono bg-zinc-100 px-1.5 py-0.5 rounded">[{ "name", "brand_id", "price", "sku", "stock", "availability_type", "discount_price", "category_id" }]</code></p>

        <textarea x-model="productImportJson" rows="10" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition" placeholder='[{"name":"Contoh","brand_id":1,"price":10000}]'></textarea>

        <button @click="doImportProducts()" :disabled="importLoading"
                class="w-full bg-zinc-900 text-white font-bold text-sm py-3.5 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition disabled:opacity-50">
            <span x-show="!importLoading" x-text="t('import','Import')"></span>
            <span x-show="importLoading" class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></span>
        </button>
    </div>
</template>
