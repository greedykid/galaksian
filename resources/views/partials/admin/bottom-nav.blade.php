<nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[430px] bg-white border-t border-zinc-200 z-50 px-2 pb-[env(safe-area-inset-bottom,0px)]">
    <div class="flex justify-around py-1.5">
        <button @click="goTo('dashboard')" class="flex flex-col items-center gap-0.5 px-2.5 py-1 rounded-lg transition"
                :class="activeView === 'dashboard' ? 'text-brand-red' : 'text-zinc-400'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-bold" x-text="t('nav_dashboard', 'Dashboard')"></span>
        </button>
        <button @click="goTo('orders')" class="flex flex-col items-center gap-0.5 px-2.5 py-1 rounded-lg transition"
                :class="['orders','order-detail'].includes(activeView) ? 'text-brand-red' : 'text-zinc-400'">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <template x-if="pendingOrdersCount > 0">
                    <span class="absolute -top-1.5 -right-2 bg-brand-red text-white text-[9px] font-bold rounded-full min-w-[16px] h-4 flex items-center justify-center px-1" x-text="pendingOrdersCount"></span>
                </template>
            </div>
            <span class="text-[10px] font-bold" x-text="t('nav_orders', 'Order')"></span>
        </button>
        <button @click="goTo('products')" class="flex flex-col items-center gap-0.5 px-2.5 py-1 rounded-lg transition"
                :class="['products','product-form','product-import'].includes(activeView) ? 'text-brand-red' : 'text-zinc-400'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-[10px] font-bold" x-text="t('nav_products', 'Produk')"></span>
        </button>
        <button @click="goTo('more')" class="flex flex-col items-center gap-0.5 px-2.5 py-1 rounded-lg transition"
                :class="activeView === 'more' ? 'text-brand-red' : 'text-zinc-400'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[10px] font-bold" x-text="t('nav_more', 'Lainnya')"></span>
        </button>
    </div>
</nav>
