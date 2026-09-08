<template x-if="activeView === 'orders'">
    <div class="px-4 py-4 space-y-3">
        <!-- Title -->
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('order_management', 'Manajemen Order')"></h1>

        <!-- Search -->
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" x-model="orderSearch" @input.debounce.400ms="searchOrders()"
                   class="w-full bg-zinc-100 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"
                   :placeholder="t('search_order_placeholder', 'Cari nomor order, nama, HP...')">
        </div>

        <!-- Status Filter Chips -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
            <button @click="filterOrderStatus = ''; loadOrders()" 
                    class="shrink-0 text-[11px] font-bold px-3 py-1.5 rounded-full transition"
                    :class="filterOrderStatus === '' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600'"
                    x-text="t('all', 'Semua')"></button>
            <template x-for="st in orderStatusList" :key="st.value">
                <button @click="filterOrderStatus = st.value; loadOrders()" 
                        class="shrink-0 text-[11px] font-bold px-3 py-1.5 rounded-full transition whitespace-nowrap"
                        :class="filterOrderStatus === st.value ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600'"
                        x-text="st.label"></button>
            </template>
        </div>

        <!-- Orders List -->
        <div class="space-y-2">
            <template x-if="ordersLoading">
                <div class="flex justify-center py-8">
                    <svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
            </template>
            <template x-for="order in orders" :key="order.id">
                <button @click="openOrderDetail(order.id)" class="w-full bg-white border border-zinc-200 rounded-xl p-3.5 hover:border-zinc-300 hover:shadow-sm transition text-left space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-zinc-900" x-text="'#' + order.order_number"></p>
                            <p class="text-[11px] text-zinc-500 mt-0.5" x-text="order.user?.name || order.user?.phone || '-'"></p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="getStatusColor(order.status)" x-text="getStatusLabel(order.status)"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] text-zinc-400" x-text="formatDate(order.created_at)"></p>
                        <p class="text-sm font-bold text-zinc-900 tabular" x-text="formatRupiah(order.grand_total || order.product_total || 0)"></p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-wrap" x-show="order.items_count > 0">
                        <span class="text-[10px] text-zinc-500" x-text="(order.items_count || order.items?.length || 0) + ' ' + t('items', 'item')"></span>
                    </div>
                </button>
            </template>
            <template x-if="orders.length === 0 && !ordersLoading">
                <p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_orders_found', 'Tidak ada order ditemukan')"></p>
            </template>
        </div>

        <!-- Pagination -->
        <template x-if="ordersMeta.last_page > 1">
            <div class="flex items-center justify-center gap-2 pt-2">
                <button @click="ordersPage--; loadOrders()" :disabled="ordersPage <= 1"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">←</button>
                <span class="text-xs font-semibold text-zinc-500" x-text="ordersPage + ' / ' + ordersMeta.last_page"></span>
                <button @click="ordersPage++; loadOrders()" :disabled="ordersPage >= ordersMeta.last_page"
                        class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">→</button>
            </div>
        </template>
    </div>
</template>
