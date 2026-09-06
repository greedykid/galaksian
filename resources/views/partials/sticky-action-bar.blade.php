<!-- ========================================================= -->
        <!-- STICKY ACTION BAR (CART & CHECKOUT)                       -->
        <!-- ========================================================= -->
        <!-- 1. Cart Sticky Action Bar (Total & Bayar) -->
        <div 
            x-show="activeTab === 'cart' && !activeSubView && cart && cart.items && cart.items.length > 0"
            class="fixed bottom-[61px] inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-white/95 backdrop-blur-md border-t border-x border-zinc-200 px-4 py-2 flex items-center justify-between shadow-[0_-4px_16px_rgba(0,0,0,0.06)]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak>
            <div class="flex flex-col">
                <span class="text-[10px] text-zinc-400 font-medium" x-text="t('total_payment_label', 'Total Pembayaran')">Total Pembayaran</span>
                <span class="text-lg font-black text-zinc-950 tabular tracking-tight leading-tight" x-text="formatRupiah(cart.pricing?.product_total || 0)"></span>
                <span class="text-[10px] text-zinc-400 leading-tight" x-text="t('not_including_shipping', 'Belum termasuk ongkir')">Belum termasuk ongkir</span>
            </div>
            <button 
                @click="proceedToCheckout()" 
                class="px-8 py-2.5 bg-[#00D06C] hover:bg-[#00B85F] active:bg-[#00A855] text-white font-extrabold text-sm rounded-xl shadow-xs active:scale-[0.98] transition min-h-[42px] flex items-center justify-center">
                <span x-text="t('pay_btn', 'Bayar')">Bayar</span>
            </button>
        </div>

        <!-- 2. Checkout Sticky Action Bar (Total Tagihan & Bayar Sekarang) -->
        <div 
            x-show="activeSubView === 'checkout'"
            class="fixed bottom-0 inset-x-0 mx-auto z-30 w-full max-w-[430px] bg-white/95 backdrop-blur-md border-t border-x border-zinc-200 px-4 py-2.5 flex items-center justify-between shadow-[0_-4px_16px_rgba(0,0,0,0.06)]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak>
            <div class="flex flex-col">
                <span class="text-[10px] text-zinc-400 font-medium" x-text="t('total_bill_label', 'Total Tagihan')">Total Tagihan</span>
                <span class="text-lg font-black text-[#1657FF] tabular tracking-tight leading-tight" x-text="formatRupiah((cart.pricing?.product_total || 0) + (isInsuranceChecked ? 2000 : 0))"></span>
                <span class="text-[10px] text-zinc-400 leading-tight" x-text="t('not_including_shipping', 'Belum termasuk ongkir')">Belum termasuk ongkir</span>
            </div>
            <button 
                @click="submitCheckout()" 
                :disabled="isSubmittingCheckout"
                class="px-6 py-2.5 bg-[#1657FF] hover:bg-blue-700 active:bg-blue-800 text-white font-extrabold text-sm rounded-xl shadow-xs flex items-center gap-1.5 transition disabled:opacity-50 active:scale-[0.98] min-h-[42px]">
                <span x-show="!isSubmittingCheckout" x-text="t('pay_now', 'Bayar Sekarang')">Bayar Sekarang</span>
                <span x-show="isSubmittingCheckout" x-text="t('processing_checkout', 'Memproses Checkout...')">Memproses Checkout...</span>
            </button>
        </div>
