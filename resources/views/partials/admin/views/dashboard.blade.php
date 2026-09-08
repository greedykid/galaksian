<template x-if="activeView === 'dashboard'">
    <div class="px-4 py-4 space-y-4">
        <!-- Welcome -->
        <div>
            <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('welcome_admin', 'Selamat datang') + ', ' + (adminUser?.name || 'Admin')"></h1>
            <p class="text-xs text-zinc-500 font-medium" x-text="new Date().toLocaleDateString(currentLang === 'id' ? 'id-ID' : 'en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'})"></p>
        </div>

        <!-- Pull to refresh -->
        <button @click="loadDashboard()" class="text-xs text-brand-red font-bold flex items-center gap-1">
            <svg class="w-3.5 h-3.5" :class="dashboardLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span x-text="t('refresh', 'Refresh')"></span>
        </button>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Revenue -->
            <div class="col-span-2 bg-gradient-to-br from-zinc-900 to-zinc-800 rounded-2xl p-4 text-white">
                <p class="text-[11px] font-semibold text-zinc-400 uppercase tracking-wider" x-text="t('total_revenue', 'Total Revenue')"></p>
                <p class="text-2xl font-extrabold mt-1 tabular" x-text="formatRupiah(dashboard.revenue?.total_paid || 0)"></p>
                <p class="text-[10px] text-zinc-500 mt-1" x-text="t('from_paid_invoices', 'Dari semua invoice terbayar')"></p>
            </div>

            <!-- Orders -->
            <div class="bg-blue-50 rounded-2xl p-3.5 space-y-1">
                <div class="w-8 h-8 rounded-xl bg-blue-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-xl font-extrabold text-zinc-900 tabular" x-text="dashboard.orders?.total || 0"></p>
                <p class="text-[11px] font-semibold text-zinc-500" x-text="t('total_orders', 'Total Order')"></p>
            </div>

            <!-- Products -->
            <div class="bg-emerald-50 rounded-2xl p-3.5 space-y-1">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="text-xl font-extrabold text-zinc-900 tabular" x-text="dashboard.products?.total || 0"></p>
                <p class="text-[11px] font-semibold text-zinc-500" x-text="t('total_products', 'Produk')"></p>
            </div>

            <!-- Pending Payment -->
            <div class="bg-amber-50 rounded-2xl p-3.5 space-y-1">
                <div class="w-8 h-8 rounded-xl bg-amber-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xl font-extrabold text-zinc-900 tabular" x-text="dashboard.orders?.pending_product_payment || 0"></p>
                <p class="text-[11px] font-semibold text-zinc-500" x-text="t('pending_payment', 'Pending Bayar')"></p>
            </div>

            <!-- Ready Delivery -->
            <div class="bg-purple-50 rounded-2xl p-3.5 space-y-1">
                <div class="w-8 h-8 rounded-xl bg-purple-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-xl font-extrabold text-zinc-900 tabular" x-text="dashboard.orders?.ready_for_delivery || 0"></p>
                <p class="text-[11px] font-semibold text-zinc-500" x-text="t('ready_delivery', 'Siap Antar')"></p>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="flex gap-2">
            <div class="flex-1 bg-zinc-100 rounded-xl p-3 text-center">
                <p class="text-lg font-extrabold text-zinc-900 tabular" x-text="dashboard.shipments?.total || 0"></p>
                <p class="text-[10px] font-semibold text-zinc-500" x-text="t('shipments', 'Shipment')"></p>
            </div>
            <div class="flex-1 bg-zinc-100 rounded-xl p-3 text-center">
                <p class="text-lg font-extrabold text-zinc-900 tabular" x-text="dashboard.trips?.active || 0"></p>
                <p class="text-[10px] font-semibold text-zinc-500" x-text="t('active_trips', 'Trip Aktif')"></p>
            </div>
            <div class="flex-1 bg-zinc-100 rounded-xl p-3 text-center">
                <p class="text-lg font-extrabold text-zinc-900 tabular" x-text="dashboard.users?.total || 0"></p>
                <p class="text-[10px] font-semibold text-zinc-500" x-text="t('users', 'User')"></p>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <template x-if="dashboard.products?.low_stock > 0">
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-red-800" x-text="t('low_stock_alert', 'Stok Rendah')"></p>
                    <p class="text-[11px] text-red-600" x-text="dashboard.products.low_stock + ' ' + t('products_low_stock', 'produk di bawah batas stok')"></p>
                </div>
            </div>
        </template>

        <!-- Recent Orders -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-extrabold text-zinc-900" x-text="t('recent_orders', 'Order Terbaru')"></h3>
                <button @click="goTo('orders')" class="text-[11px] font-bold text-brand-red" x-text="t('view_all', 'Lihat Semua →')"></button>
            </div>
            <div class="space-y-2">
                <template x-for="order in recentOrders.slice(0, 5)" :key="order.id">
                    <button @click="openOrderDetail(order.id)" class="w-full bg-zinc-50 rounded-xl p-3 flex items-center gap-3 hover:bg-zinc-100 transition text-left">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold text-zinc-900 truncate" x-text="'#' + order.order_number"></p>
                                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="getStatusColor(order.status)" x-text="getStatusLabel(order.status)"></span>
                            </div>
                            <p class="text-[11px] text-zinc-500 mt-0.5 truncate" x-text="(order.user?.name || order.user?.phone || '-')"></p>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 tabular shrink-0" x-text="formatRupiah(order.grand_total || order.product_total || 0)"></p>
                    </button>
                </template>
                <template x-if="recentOrders.length === 0 && !ordersLoading">
                    <p class="text-center text-xs text-zinc-400 py-4" x-text="t('no_orders_yet', 'Belum ada order')"></p>
                </template>
            </div>
        </div>
    </div>
</template>
