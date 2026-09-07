<!-- ========================================================= -->
            <!-- VIEW 3: TRANSAKSI (2 TABS: PENDING & SELESAI)              -->
            <!-- ========================================================= -->
            <div x-show="activeTab === 'transactions' && !activeSubView" class="space-y-4">
                <!-- 1. Dedicated Top Header (Consistent Royal Blue #1657FF) -->
                <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 shadow-xs -mx-px w-[calc(100%+2px)]">
                    <div class="flex items-center justify-between">
                        <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('transaction_history', 'Transaksi')">Transaksi</h1>
                        <div class="flex items-center gap-1.5">
                            <!-- Search Toggle Button -->
                            <button 
                                @click="transactionSearchOpen = !transactionSearchOpen; if (transactionSearchOpen) $nextTick(() => $refs.transSearchInput?.focus())" 
                                class="w-8 h-8 rounded-xl hover:bg-white/10 flex items-center justify-center text-white transition active:scale-95 cursor-pointer" 
                                :title="t('search_transactions_title', 'Cari Transaksi')">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                            <!-- Refresh Icon Only Button -->
                            <button 
                                @click="fetchOrders(); showToast(t('refreshing_trans_toast', 'Memperbarui transaksi...'))" 
                                class="w-8 h-8 rounded-xl hover:bg-white/10 flex items-center justify-center text-white transition active:scale-95 cursor-pointer" 
                                :title="t('refresh_transactions_title', 'Segarkan Transaksi')">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                    <path d="M3 3v5h5"></path>
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
                                    <path d="M16 16h5v5"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Search Bar -->
                    <div x-show="transactionSearchOpen" x-transition class="mt-2.5 pt-1" x-cloak>
                        <div class="relative">
                            <input 
                                x-ref="transSearchInput"
                                type="text" 
                                x-model="transactionSearchQuery" 
                                @input.debounce.300ms="fetchOrders()"
                                :placeholder="t('search_trans_placeholder', 'Cari no. pesanan atau nama produk...')" 
                                class="w-full pl-9 pr-8 py-2 rounded-xl bg-white text-zinc-900 text-xs placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-300 shadow-2xs">
                            <svg class="w-4 h-4 text-zinc-400 stroke-current fill-none absolute left-3 top-2.5" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <button x-show="transactionSearchQuery" @click="transactionSearchQuery = ''; fetchOrders()" class="absolute right-2.5 top-2.5 text-zinc-400 hover:text-zinc-600">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3 STRICT TABS: BERLANGSUNG, SELESAI, DIBATALKAN -->
                <div class="px-4 pt-1">
                    <div class="grid grid-cols-3 bg-zinc-100 p-1 rounded-2xl text-xs font-bold text-center border border-zinc-200/70">
                        <button 
                            @click="transactionTab = 'berlangsung'; fetchOrders()" 
                            :class="transactionTab === 'berlangsung' ? 'bg-white text-blue-600 shadow-2xs' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-xl transition cursor-pointer">
                            <span x-text="t('tab_ongoing', 'Berlangsung')">Berlangsung</span>
                        </button>
                        <button 
                            @click="transactionTab = 'selesai'; fetchOrders()" 
                            :class="transactionTab === 'selesai' ? 'bg-white text-blue-600 shadow-2xs' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-xl transition cursor-pointer">
                            <span x-text="t('tab_completed', 'Selesai')">Selesai</span>
                        </button>
                        <button 
                            @click="transactionTab = 'dibatalkan'; fetchOrders()" 
                            :class="transactionTab === 'dibatalkan' ? 'bg-white text-blue-600 shadow-2xs' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-xl transition cursor-pointer">
                            <span x-text="t('tab_cancelled', 'Dibatalkan')">Dibatalkan</span>
                        </button>
                    </div>
                </div>

                <!-- Auth Guard Notice -->
                <template x-if="!isLoggedIn">
                    <div class="p-10 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <h4 class="font-bold text-sm text-zinc-900" x-text="t('transaction_access_locked', 'Akses Transaksi Terkunci')">Akses Transaksi Terkunci</h4>
                        <p class="text-xs text-zinc-500" x-text="t('login_to_check_orders', 'Silakan login untuk memeriksa riwayat dan status pesanan.')">Silakan login untuk memeriksa riwayat dan status pesanan.</p>
                        <button @click="quickLoginDemo()" class="px-4 py-2 bg-zinc-950 text-white font-bold text-xs rounded-xl hover:bg-zinc-800 transition" x-text="t('quick_login_demo_btn', 'Login Cepat Demo User (081234567890)')">
                            Login Cepat Demo User (081234567890)
                        </button>
                    </div>
                </template>

                <!-- Order Listing -->
                <template x-if="isLoggedIn">
                    <div class="px-4 space-y-3 pb-6">
                        <!-- Loading State: Skeleton Cards -->
                        <div x-show="ordersLoading" x-cloak class="space-y-3">
                            <div x-for="i in 3" :key="i" class="bg-white border border-zinc-200 rounded-2xl p-4 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 animate-pulse"></div>
                                        <div class="space-y-1.5">
                                            <div class="w-24 h-2.5 rounded bg-zinc-200 animate-pulse"></div>
                                            <div class="w-28 h-2 rounded bg-zinc-100 animate-pulse"></div>
                                        </div>
                                    </div>
                                    <div class="w-16 h-5 rounded-full bg-zinc-100 animate-pulse"></div>
                                </div>
                                <div class="flex items-center gap-2.5 pt-1">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-100 animate-pulse"></div>
                                    <div class="flex-1 space-y-1.5">
                                        <div class="w-3/4 h-2.5 rounded bg-zinc-200 animate-pulse"></div>
                                        <div class="w-1/2 h-2 rounded bg-zinc-100 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                                    <div class="w-20 h-3 rounded bg-zinc-100 animate-pulse"></div>
                                    <div class="w-24 h-6 rounded-lg bg-zinc-200 animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        <template x-if="!ordersLoading && getFilteredOrders().length === 0">
                            <div class="p-10 text-center space-y-2">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto">
                                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <h4 class="font-bold text-xs text-zinc-800" x-text="t('no_orders', 'Tidak Ada Pesanan')">Tidak Ada Pesanan</h4>
                                <p class="text-[11px] text-zinc-400" x-text="transactionTab === 'berlangsung' ? t('no_ongoing_transactions', 'Belum ada transaksi yang sedang berjalan.') : (transactionTab === 'selesai' ? t('no_completed_transactions', 'Belum ada transaksi yang selesai.') : t('no_cancelled_transactions', 'Tidak ada transaksi yang dibatalkan.'))"></p>
                            </div>
                        </template>

                        <!-- Order Card: Simple, Elegant, Modern E-Commerce Standard -->
                        <template x-for="order in getFilteredOrders()" :key="order.id">
                            <div 
                                @click="openOrderDetail(order.id)" 
                                class="bg-white border border-zinc-200 hover:border-zinc-300 active:border-zinc-400 rounded-2xl p-4 space-y-3 shadow-2xs hover:shadow-xs transition cursor-pointer group active:scale-[0.99]">
                                
                                <!-- 1. Header: Icon, Date, Order No & Status Badge -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-600 shrink-0">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-zinc-900" x-text="t('shopping', 'Belanja')">Belanja</span>
                                                <span class="text-zinc-300">•</span>
                                                <span class="text-[11px] text-zinc-500 font-medium" x-text="formatDate(order.created_at)"></span>
                                            </div>
                                            <span class="text-[10px] font-mono text-zinc-400 truncate block" x-text="order.order_number"></span>
                                        </div>
                                    </div>
                                    <span 
                                        :class="getOrderStatusColor(order.status)"
                                        class="text-[10px] font-bold px-2.5 py-0.5 rounded border shrink-0" 
                                        x-text="getOrderStatusLabel(order.status)">
                                    </span>
                                </div>

                                <!-- 2. Product Preview (Thumbnail + Name + Qty Summary) -->
                                <div class="flex items-center gap-3 py-0.5" x-show="order.items && order.items.length > 0">
                                    <img 
                                        :src="getFallbackImage({ name: (order.items && order.items.length > 0 ? (order.items[0]?.product_name || order.items[0]?.name) : '') })" 
                                        class="w-12 h-12 rounded-xl object-cover bg-zinc-50 border border-zinc-200/80 shrink-0 shadow-2xs" 
                                        alt="Product Thumbnail">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-bold text-zinc-900 truncate" x-text="order.items && order.items.length > 0 ? (order.items[0]?.product_name || order.items[0]?.name || t('ordered_product_fallback', 'Produk Pesanan')) : t('ordered_product_fallback', 'Produk Pesanan')"></h4>
                                        <p class="text-[11px] text-zinc-500 mt-0.5 flex items-center gap-1 flex-wrap">
                                            <span x-text="(order.items && order.items.length > 0 ? order.items[0]?.qty : 1) + ' ' + t('items_unit', 'barang')"></span>
                                            <template x-if="order.items && order.items.length > 1">
                                                <span class="text-zinc-400 font-medium" x-text="'• +' + (order.items.length - 1) + ' ' + t('other_products', 'produk lainnya')"></span>
                                            </template>
                                        </p>
                                    </div>
                                </div>

                                <!-- 3. Footer: Total Belanja & Single Clear Action Button -->
                                <div class="pt-3 border-t border-zinc-100 flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] text-zinc-400 font-medium block" x-text="t('total_shopping', 'Total Belanja')">Total Belanja</span>
                                        <span class="font-extrabold text-sm text-zinc-950 tabular" x-text="formatRupiah(order.grand_total || order.product_total)"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <!-- Conditional Pay button if pending -->
                                        <template x-if="order.status === 'pending_payment_product' || (getShippingInvoice(order) && getShippingInvoice(order).status !== 'paid' && isProductInvoicePaid(order))">
                                            <button 
                                                @click.stop="openOrderDetail(order.id)" 
                                                class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-2xs min-h-[34px]">
                                                <span x-text="t('pay_now', 'Bayar')">Bayar</span>
                                            </button>
                                        </template>

                                        <!-- Buy Again if Completed -->
                                        <template x-if="order.status === 'completed'">
                                            <button 
                                                @click.stop="reorderItems(order)" 
                                                class="px-3 py-1.5 bg-[#E60012] hover:bg-red-700 text-white rounded-lg text-xs font-bold transition min-h-[34px] shadow-2xs" 
                                                x-text="t('buy_again_btn', 'Beli Lagi')">
                                                Beli Lagi
                                            </button>
                                        </template>

                                        <!-- Primary Detail Action -->
                                        <button 
                                            @click.stop="openOrderDetail(order.id)" 
                                            class="px-3.5 py-1.5 bg-white border border-zinc-200 hover:bg-zinc-50 active:bg-zinc-100 text-zinc-800 rounded-lg text-xs font-semibold transition flex items-center gap-1 min-h-[34px] shadow-2xs group-hover:border-zinc-300">
                                            <span x-text="t('view_detail', 'Lihat Detail')">Lihat Detail</span>
                                            <svg class="w-3.5 h-3.5 stroke-current fill-none text-zinc-400 group-hover:text-zinc-700 transition" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
