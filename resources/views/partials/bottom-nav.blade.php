<!-- ========================================================= -->
        <!-- STICKY BOTTOM NAVIGATION BAR (FIXED & RESILIENT)          -->
        <!-- ========================================================= -->
        <nav class="fixed bottom-0 inset-x-0 mx-auto z-40 w-full max-w-[430px] bg-white border-t border-zinc-200 px-3 py-2 flex items-center justify-around shadow-[0_-4px_20px_rgba(0,0,0,0.04)]">
            <!-- 1. Home / Beranda -->
            <button 
                @click="goToTab('home')" 
                :class="activeTab === 'home' && !activeSubView ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
                class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition">
                <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span class="text-[10px] tracking-tight" x-text="t('nav_home', 'Beranda')">Beranda</span>
            </button>

            <!-- 2. Cart / Keranjang + Live Count -->
            <button 
                @click="goToTab('cart')" 
                :class="activeTab === 'cart' && !activeSubView ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
                class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition relative">
                <div class="relative">
                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                        <path d="M3 6h18"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <!-- Live Item Count Badge -->
                    <template x-if="cart.total_qty > 0">
                        <span class="absolute -top-1.5 -right-2.5 bg-[#00D06C] text-white text-[9px] font-black px-1.5 py-0.2 rounded-full tabular shadow-2xs" x-text="cart.total_qty"></span>
                    </template>
                </div>
                <span class="text-[10px] tracking-tight" x-text="t('nav_cart', 'Keranjang')">Keranjang</span>
            </button>

            <!-- 3. Transactions / Transaksi -->
            <button 
                @click="goToTab('transactions')" 
                :class="(activeTab === 'transactions' || activeSubView === 'order-detail' || activeSubView === 'qris-payment') ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
                class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition">
                <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                    <path d="M14 8H8"></path>
                    <path d="M16 12H8"></path>
                    <path d="M13 16H8"></path>
                </svg>
                <span class="text-[10px] tracking-tight" x-text="t('nav_transactions', 'Transaksi')">Transaksi</span>
            </button>

            <!-- 4. Profile / Profil -->
            <button 
                @click="goToTab('profile')" 
                :class="activeTab === 'profile' && !activeSubView ? 'text-[#1657FF] font-bold' : 'text-zinc-400 hover:text-zinc-600 font-medium'"
                class="flex flex-col items-center gap-1 min-w-[56px] min-h-[44px] justify-center transition">
                <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="text-[10px] tracking-tight" x-text="t('nav_profile', 'Profil')">Profil</span>
            </button>
        </nav>
