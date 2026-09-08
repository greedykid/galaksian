<template x-if="activeView === 'product-form'">
    <div class="px-4 py-4 space-y-4">
        <button @click="goTo('products')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_products', 'Kembali ke Produk')"></span>
        </button>

        <h1 class="text-lg font-extrabold text-zinc-900" x-text="productForm.id ? t('edit_product','Edit Produk') : t('new_product','Produk Baru')"></h1>

        <div class="space-y-3">
            <div>
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('product_name','Nama Produk')"></label>
                <input type="text" x-model="productForm.name" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition" :placeholder="t('product_name_placeholder','Nama produk...')">
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">SKU</label>
                    <input type="text" x-model="productForm.sku" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Slug</label>
                    <input type="text" x-model="productForm.slug" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('price','Harga')"></label>
                    <input type="number" x-model.number="productForm.price" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold tabular focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('discount_price','Harga Diskon')"></label>
                    <input type="number" x-model.number="productForm.discount_price" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold tabular focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('stock','Stok')"></label>
                    <input type="number" x-model.number="productForm.stock" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold tabular focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('low_stock','Batas Stok Rendah')"></label>
                    <input type="number" x-model.number="productForm.low_stock_threshold" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold tabular focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
            </div>
            <div class="grid grid-cols-1 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('availability','Ketersediaan')"></label>
                    <select x-model="productForm.availability_type" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                        <option value="ready_stock" x-text="t('ready_stock','Ready Stock')"></option>
                        <option value="open_po" x-text="t('open_po','Open PO')"></option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('brand','Brand')"></label>
                    <select x-model="productForm.brand_id" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                        <option value="">-</option>
                        <template x-for="b in brands" :key="b.id"><option :value="b.id" x-text="b.name"></option></template>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('category','Kategori')"></label>
                    <select x-model="productForm.category_id" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                        <option value="">-</option>
                        <template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('description','Deskripsi')"></label>
                <textarea x-model="productForm.description" rows="3" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"></textarea>
            </div>
            <label class="flex items-center gap-2.5">
                <input type="checkbox" x-model="productForm.is_active" class="w-4 h-4 rounded border-zinc-300 text-brand-red focus:ring-brand-red">
                <span class="text-xs font-semibold text-zinc-700" x-text="t('product_active','Produk aktif')"></span>
            </label>

            <!-- GAMBAR PRODUK -->
            <template x-if="productForm.id">
                <div class="space-y-2.5 border-t border-zinc-100 pt-3">
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('product_images','Gambar Produk')"></label>

                    <!-- Galeri existing -->
                    <div x-show="productImages.length" class="grid grid-cols-3 gap-2">
                        <template x-for="(img, idx) in productImages" :key="img.id || idx">
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-zinc-100 border border-zinc-200">
                                <img :src="img.path" class="w-full h-full object-cover" alt="">
                                <span x-show="img.is_primary" class="absolute top-1 left-1 text-[8px] font-bold bg-brand-red text-white px-1.5 py-0.5 rounded" x-text="t('primary','Utama')"></span>
                                <button @click="doDeleteProductImage(img.id)" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-zinc-900/70 text-white flex items-center justify-center hover:bg-red-600 transition" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Upload -->
                    <div class="flex items-center gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="file" accept="image/*" multiple @change="onProductImagesSelected($event)" class="hidden">
                            <span class="w-full flex items-center justify-center gap-2 bg-zinc-100 text-zinc-600 text-xs font-bold py-3 rounded-xl hover:bg-zinc-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4"/><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                                <span x-text="t('choose_images','Pilih Gambar')"></span>
                            </span>
                        </label>
                        <button @click="doUploadProductImages()" :disabled="productImageUploading || !productImageFiles.length"
                                class="shrink-0 bg-zinc-900 text-white text-xs font-bold px-4 py-3 rounded-xl hover:bg-zinc-800 transition disabled:opacity-40">
                            <span x-show="!productImageUploading" x-text="t('upload','Upload')"></span>
                            <span x-show="productImageUploading" class="inline-flex"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></span>
                        </button>
                    </div>
                    <p class="text-[10px] text-zinc-400" x-text="t('image_hint','Maks 5 gambar, JPG/PNG/WebP, 3MB per file')"></p>
                </div>
            </template>
        </div>

        <button @click="saveProduct()" :disabled="productFormLoading"
                class="w-full bg-zinc-900 text-white font-bold text-sm py-3.5 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition disabled:opacity-50">
            <span x-show="!productFormLoading" x-text="t('save','Simpan')"></span>
            <span x-show="productFormLoading" class="flex items-center justify-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></span>
        </button>
    </div>
</template>
