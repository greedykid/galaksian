<template x-if="activeView === 'refunds'">
    <div class="px-4 py-4 space-y-3">
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('refund_management', 'Manajemen Refund')"></h1>
        <div class="space-y-2">
            <template x-if="refundsLoading"><div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></div></template>
            <template x-for="r in refunds" :key="r.id">
                <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-zinc-900" x-text="'#' + (r.id)"></p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                              :class="r.status === 'approved' || r.status === 'completed' ? 'bg-green-100 text-green-700' : r.status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700'"
                              x-text="r.status || '-'"></span>
                    </div>
                    <p class="text-xs font-bold text-zinc-900 tabular" x-text="formatRupiah(r.amount || 0)"></p>
                    <p class="text-[11px] text-zinc-500" x-text="r.order?.order_number || ('Order #' + r.order_id)"></p>
                    <p class="text-[11px] text-zinc-500" x-text="r.reason || '-'"></p>
                    <div class="flex gap-2 pt-1 border-t border-zinc-100" x-show="r.status === 'pending'">
                        <button @click="decideRefund(r.id, 'approve')" class="flex-1 text-[11px] font-bold py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition" x-text="t('approve','Setujui')"></button>
                        <button @click="decideRefund(r.id, 'reject')" class="flex-1 text-[11px] font-bold py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition" x-text="t('reject','Tolak')"></button>
                    </div>
                </div>
            </template>
            <template x-if="refunds.length === 0 && !refundsLoading"><p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_refunds','Belum ada refund')"></p></template>
        </div>
    </div>
</template>
