<template x-if="activeView === 'trips'">
    <div class="px-4 py-4 space-y-3">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('trip_management', 'Manajemen Trip')"></h1>
            <button @click="tripForm = { id: null, code: '', origin_country: 'ID', destination_country: 'JP', departure_at: '', arrival_at: '', cutoff_at: '', status: 'draft', notes: '' }; activeView='trip-form'" class="bg-zinc-900 text-white text-xs font-bold px-3 py-2 rounded-xl hover:bg-zinc-800 transition" x-text="t('add_trip','+ Trip')"></button>
        </div>
        <div class="space-y-2">
            <template x-if="tripsLoading"><div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></div></template>
            <template x-for="t in trips" :key="t.id">
                <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                    <div class="flex justify-between">
                        <p class="text-xs font-bold text-zinc-900" x-text="t.code || ('#'+t.id)"></p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="t.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500'" x-text="t.status"></span>
                    </div>
                    <p class="text-[11px] text-zinc-500" x-text="(t.origin_country || '') + ' → ' + (t.destination_country || '') + ' · ' + (t.departure_at ? formatDate(t.departure_at) : '-')"></p>
                    <div class="flex gap-2 pt-1 border-t border-zinc-100 justify-end">
                        <button @click="editTrip(t)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button @click="deleteTrip(t.id)" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-red-50 flex items-center justify-center text-zinc-500 hover:text-red-600 transition" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2m-6 4v6m4-6v6"/></svg>
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="trips.length === 0 && !tripsLoading"><p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_trips','Belum ada trip')"></p></template>
        </div>
    </div>
</template>
