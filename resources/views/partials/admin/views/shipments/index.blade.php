<template x-if="activeView === 'shipments'">
    <div class="px-4 py-4 space-y-3">
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
                    <div class="flex gap-2 pt-1 border-t border-zinc-100">
                        <button @click="activeView='shipment-form'" class="text-[11px] font-bold text-blue-600 hover:underline" x-text="t('edit','Edit')"></button>
                        <button @click="sendBagasian(s.id)" class="text-[11px] font-bold text-amber-600 hover:underline" x-text="t('send_bagasian','Kirim Bagasian')"></button>
                    </div>
                </div>
            </template>
            <template x-if="shipments.length === 0 && !shipmentsLoading"><p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_shipments','Belum ada shipment')"></p></template>
        </div>
    </div>
</template>
