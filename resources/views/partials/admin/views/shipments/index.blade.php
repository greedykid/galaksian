<template x-if="activeView === 'shipments'">
    <div class="px-4 py-4 space-y-3">
        <button @click="goTo('more')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_menu', 'Kembali ke Menu')"></span>
        </button>
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('shipment_management', 'Manajemen Shipment')"></h1>
            <button @click="activeView='shipment-form'" class="bg-zinc-900 text-white text-xs font-bold px-3 py-2 rounded-xl hover:bg-zinc-800 transition" x-text="t('add_shipment','+ Shipment')"></button>
        </div>
        <div class="space-y-2">
            <template x-if="shipmentsLoading"><div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></div></template>
            <template x-for="s in shipments" :key="s.id">
                <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                    <div class="flex justify-between">
                        <p class="text-xs font-bold text-zinc-900" x-text="'#' + (s.shipment_number || s.id)"></p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="s.status === 'sent_to_bagasian' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500'" x-text="s.status || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] text-zinc-500" x-text="(s.origin_country || '') + ' → ' + (s.destination_country || '')"></p>
                        <p class="text-[11px] text-zinc-500" x-text="(s.orders_count || 0) + ' order'"></p>
                    </div>
                    <div class="flex gap-2 pt-1 border-t border-zinc-100 justify-end">
                        <button @click="sendBagasian(s.id)" class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 flex items-center justify-center text-amber-600 transition" title="Kirim Bagasian">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                        <button @click="activeView='shipment-form'" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="shipments.length === 0 && !shipmentsLoading"><p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_shipments','Belum ada shipment')"></p></template>
        </div>
    </div>
</template>
