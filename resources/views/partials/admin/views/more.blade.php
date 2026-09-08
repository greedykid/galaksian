<template x-if="activeView === 'more'">
    <div class="px-4 py-4 space-y-3">
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('more_menu', 'Menu Lainnya')"></h1>
        <div class="space-y-2">
            <button @click="goTo('shipments')" class="w-full bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3 hover:border-zinc-300 transition text-left">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">🚚</div>
                <div class="flex-1"><p class="text-sm font-bold text-zinc-900" x-text="t('nav_shipments','Shipment')"></p><p class="text-[11px] text-zinc-500" x-text="t('shipment_desc','Kelola pengiriman & Bagasian')"></p></div>
                <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button @click="goTo('trips')" class="w-full bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3 hover:border-zinc-300 transition text-left">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">✈️</div>
                <div class="flex-1"><p class="text-sm font-bold text-zinc-900" x-text="t('nav_trips','Trip')"></p><p class="text-[11px] text-zinc-500" x-text="t('trip_desc','Kelola jadwal trip jastip')"></p></div>
                <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button @click="goTo('refunds')" class="w-full bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3 hover:border-zinc-300 transition text-left">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">💰</div>
                <div class="flex-1"><p class="text-sm font-bold text-zinc-900" x-text="t('nav_refunds','Refund')"></p><p class="text-[11px] text-zinc-500" x-text="t('refund_desc','Proses pengajuan refund')"></p></div>
                <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button @click="goTo('users')" class="w-full bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3 hover:border-zinc-300 transition text-left">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">👥</div>
                <div class="flex-1"><p class="text-sm font-bold text-zinc-900" x-text="t('nav_users','User')"></p><p class="text-[11px] text-zinc-500" x-text="t('user_desc','Lihat & kelola pelanggan')"></p></div>
                <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button @click="goTo('brands')" class="w-full bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3 hover:border-zinc-300 transition text-left">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">🏷️</div>
                <div class="flex-1"><p class="text-sm font-bold text-zinc-900" x-text="t('nav_catalog','Katalog')"></p><p class="text-[11px] text-zinc-500" x-text="t('catalog_desc','Brand, kategori, banner')"></p></div>
                <svg class="w-4 h-4 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</template>
