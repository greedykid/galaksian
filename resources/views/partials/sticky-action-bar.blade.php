<!-- ========================================================= -->
        <!-- STICKY ACTION BAR ABOVE BOTTOM NAV (CART & CHECKOUT)      -->
        <!-- ========================================================= -->
        <!-- 1. Cart Sticky Action Bar (Total & Lanjut ke Pembayaran) -->
        <div 
            x-show="activeTab === 'cart' && !activeSubView && cart && cart.items && cart.items.length > 0"
            class="fixed bottom-[61px] inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-white/95 backdrop-blur-md border-t border-zinc-200/90 px-4 py-2.5 flex items-center justify-between shadow-[0_-4px_16px_rgba(0,0,0,0.06)]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak>
            <div class="flex flex-col">
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider" x-text="t('total_payment', 'Total Pembayaran')">Total Pembayaran</span>
                <span class="text-base font-black text-[#E60012] tabular tracking-tight" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
            </div>
            <button 
                @click="proceedToCheckout()" 
                class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 active:bg-zinc-900 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition active:scale-[0.98] min-h-[44px]">
                <span x-text="t('proceed_to_payment', 'Lanjut ke Pembayaran')">Lanjut ke Pembayaran</span>
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- 2. Checkout Sticky Action Bar (Total & Bayar Sekarang) -->
        <div 
            x-show="activeSubView === 'checkout'"
            class="fixed bottom-[61px] inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-white/95 backdrop-blur-md border-t border-zinc-200/90 px-4 py-2.5 flex items-center justify-between shadow-[0_-4px_16px_rgba(0,0,0,0.06)]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak>
            <div class="flex flex-col">
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider" x-text="t('total_payment', 'Total Pembayaran')">Total Pembayaran</span>
                <span class="text-base font-black text-[#E60012] tabular tracking-tight" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
            </div>
            <button 
                @click="submitCheckout()" 
                :disabled="isSubmittingCheckout"
                class="px-5 py-2.5 bg-zinc-950 hover:bg-zinc-800 active:bg-zinc-900 text-white font-bold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition disabled:opacity-50 active:scale-[0.98] min-h-[44px]">
                <span x-show="!isSubmittingCheckout" x-text="t('pay_now', 'Bayar Sekarang')">Bayar Sekarang</span>
                <span x-show="isSubmittingCheckout" x-text="t('processing_checkout', 'Memproses Checkout...')">Memproses Checkout...</span>
                <svg x-show="!isSubmittingCheckout" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>
