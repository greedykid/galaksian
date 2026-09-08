<template x-if="activeView === 'trip-form'">
    <div class="px-4 py-4 space-y-4">
        <button @click="goTo('trips')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_trips','Kembali ke Trip')"></span>
        </button>
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('trip_form','Form Trip')"></h1>
        <div class="space-y-3">
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Kode</label>
                <input type="text" x-model="tripForm.code" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Asal</label>
                    <select x-model="tripForm.origin_country" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"><option value="ID">ID</option><option value="JP">JP</option></select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Tujuan</label>
                    <select x-model="tripForm.destination_country" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"><option value="JP">JP</option><option value="ID">ID</option></select>
                </div>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Berangkat</label>
                <input type="datetime-local" x-model="tripForm.departure_at" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Pulang</label>
                <input type="datetime-local" x-model="tripForm.arrival_at" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Cutoff</label>
                <input type="datetime-local" x-model="tripForm.cutoff_at" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Status</label>
                <select x-model="tripForm.status" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                    <option value="draft">Draft</option><option value="active">Aktif</option><option value="completed">Selesai</option><option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Catatan</label>
                <textarea x-model="tripForm.notes" rows="3" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"></textarea>
            </div>
        </div>
        <button @click="saveTrip()" class="w-full bg-zinc-900 text-white font-bold text-sm py-3.5 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition" x-text="t('save','Simpan')"></button>
    </div>
</template>
