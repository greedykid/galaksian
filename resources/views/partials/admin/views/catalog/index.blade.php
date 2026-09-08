<template x-if="activeView === 'brands' || activeView === 'categories' || activeView === 'banners'">
    <div class="px-4 py-4 space-y-3">
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('catalog_management', 'Manajemen Katalog')"></h1>

        <!-- Tabs -->
        <div class="flex gap-2 bg-zinc-100 rounded-xl p-1">
            <button @click="activeView='brands'; loadCatalog('brands')" class="flex-1 py-2 text-xs font-bold rounded-lg transition" :class="activeView === 'brands' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500'" x-text="t('nav_brands','Brand')"></button>
            <button @click="activeView='categories'; loadCatalog('categories')" class="flex-1 py-2 text-xs font-bold rounded-lg transition" :class="activeView === 'categories' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500'" x-text="t('nav_categories','Kategori')"></button>
            <button @click="activeView='banners'; loadCatalog('banners')" class="flex-1 py-2 text-xs font-bold rounded-lg transition" :class="activeView === 'banners' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500'" x-text="t('nav_banners','Banner')"></button>
        </div>

        <!-- Add button -->
        <button @click="catalogAddNew()" class="w-full bg-zinc-900 text-white text-xs font-bold py-3 rounded-xl hover:bg-zinc-800 transition" x-text="catalogAddLabel()"></button>

        <!-- BRANDS -->
        <template x-if="activeView === 'brands'">
            <div class="space-y-2">
                <template x-for="b in brands" :key="b.id">
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-zinc-900" x-text="b.name"></p>
                            <p class="text-[10px] text-zinc-400" x-text="b.slug || ''"></p>
                        </div>
                        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full" :class="b.is_active ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500'" x-text="b.is_active ? t('active','Aktif') : t('inactive','Nonaktif')"></span>
                        <button @click="catalogEdit(b,'brand')" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                        <button @click="deleteCatalog('brands', b.id)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-red-50 flex items-center justify-center text-zinc-500 hover:text-red-600 transition" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/></svg></button>
                    </div>
                </template>
                <template x-if="brands.length === 0"><p class="text-center text-xs text-zinc-400 py-6" x-text="t('no_brands','Belum ada brand')"></p></template>
            </div>
        </template>

        <!-- CATEGORIES -->
        <template x-if="activeView === 'categories'">
            <div class="space-y-2">
                <template x-for="c in categories" :key="c.id">
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-zinc-900" x-text="c.name"></p>
                            <p class="text-[10px] text-zinc-400" x-text="c.slug || ''"></p>
                        </div>
                        <button @click="catalogEdit(c,'category')" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                        <button @click="deleteCatalog('categories', c.id)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-red-50 flex items-center justify-center text-zinc-500 hover:text-red-600 transition" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/></svg></button>
                    </div>
                </template>
                <template x-if="categories.length === 0"><p class="text-center text-xs text-zinc-400 py-6" x-text="t('no_categories','Belum ada kategori')"></p></template>
            </div>
        </template>

        <!-- BANNERS -->
        <template x-if="activeView === 'banners'">
            <div class="space-y-2">
                <template x-for="b in banners" :key="b.id">
                    <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-zinc-900" x-text="b.title"></p>
                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full" :class="b.is_active ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500'" x-text="b.is_active ? t('active','Aktif') : t('inactive','Nonaktif')"></span>
                        </div>
                        <p class="text-[10px] text-zinc-400" x-text="(b.locale || '') + ' · ' + (b.country_filter || '')"></p>
                        <div class="flex gap-2 pt-1 border-t border-zinc-100 justify-end">
                            <button @click="catalogEdit(b,'banner')" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button @click="deleteCatalog('banners', b.id)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-red-50 flex items-center justify-center text-zinc-500 hover:text-red-600 transition" title="Hapus"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/></svg></button>
                        </div>
                    </div>
                </template>
                <template x-if="banners.length === 0"><p class="text-center text-xs text-zinc-400 py-6" x-text="t('no_banners','Belum ada banner')"></p></template>
            </div>
        </template>
    </div>
</template>
