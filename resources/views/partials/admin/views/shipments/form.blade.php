<template x-if="activeView === 'shipment-form'">
    <div class="px-4 py-4 space-y-4">
        <button @click="goTo('shipments')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_shipments','Kembali ke Shipment')"></span>
        </button>
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('shipment_form','Form Shipment')"></h1>
        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Nomor</label>
                    <input type="text" x-model="shipmentForm.shipment_number" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Trip ID</label>
                    <input type="number" x-model.number="shipmentForm.trip_id" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Asal</label>
                    <select x-model="shipmentForm.origin_country" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"><option value="ID">ID</option><option value="JP">JP</option></select>
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Tujuan</label>
                    <select x-model="shipmentForm.destination_country" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"><option value="JP">JP</option><option value="ID">ID</option></select>
                </div>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Ref Bagasian</label>
                <input type="text" x-model="shipmentForm.bagasian_reference" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Estimasi Berat</label>
                    <input type="text" x-model="shipmentForm.packing_estimate_weight" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
                <div>
                    <label class="text-[11px] font-semibold text-zinc-600">Estimasi Volume</label>
                    <input type="text" x-model="shipmentForm.packing_estimate_volume" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                </div>
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Estimasi Biaya</label>
                <input type="number" x-model.number="shipmentForm.packing_estimate_cost" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-semibold tabular focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Catatan</label>
                <textarea x-model="shipmentForm.notes" rows="3" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"></textarea>
            </div>
        </div>
        <button @click="saveShipment()" class="w-full bg-zinc-900 text-white font-bold text-sm py-3.5 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition" x-text="t('save','Simpan')"></button>
    </div>
</template>
