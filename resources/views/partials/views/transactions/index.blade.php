<!-- ========================================================= -->
            <!-- VIEW 3: TRANSAKSI (2 TABS: PENDING & SELESAI)              -->
            <!-- ========================================================= -->
            <div x-show="activeTab === 'transactions' && !activeSubView" class="space-y-4">
                <!-- Header -->
                <div class="px-4 pt-3 pb-2 border-b border-zinc-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-zinc-950 tracking-tight" x-text="t('transaction_history', 'Daftar Transaksi')">Daftar Transaksi</h2>
                        <p class="text-[11px] text-zinc-500" x-text="t('transactions_subtitle', 'Pantau progres belanja jastip & status pengiriman')">Pantau progres belanja jastip & status pengiriman</p>
                    </div>
                    <button @click="fetchOrders()" class="text-xs font-semibold text-zinc-600 hover:text-zinc-950">
                        Refresh
                    </button>
                </div>

                <!-- 2 STRICT TABS: PENDING & SELESAI -->
                <div class="px-4">
                    <div class="grid grid-cols-2 bg-zinc-100 p-0.5 rounded-xl text-xs font-bold text-center border border-zinc-200/60">
                        <button 
                            @click="transactionTab = 'pending'; fetchOrders()" 
                            :class="transactionTab === 'pending' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-lg transition">
                            <span x-text="t('tab_pending', 'Transaksi Pending')">Transaksi Pending</span>
                        </button>
                        <button 
                            @click="transactionTab = 'completed'; fetchOrders()" 
                            :class="transactionTab === 'completed' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-lg transition">
                            <span x-text="t('tab_completed', 'Transaksi Selesai')">Transaksi Selesai</span>
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
                        <template x-if="orders.length === 0">
                            <div class="p-10 text-center space-y-2">
                                <div class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-400 flex items-center justify-center mx-auto">
                                    <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                </div>
                                <h4 class="font-bold text-xs text-zinc-800" x-text="t('no_orders', 'Tidak Ada Pesanan')">Tidak Ada Pesanan</h4>
                                <p class="text-[11px] text-zinc-400" x-text="transactionTab === 'pending' ? t('no_pending_orders', 'Belum ada transaksi yang sedang berjalan.') : t('no_completed_orders', 'Belum ada transaksi yang selesai.')"></p>
                            </div>
                        </template>

                        <!-- Order Card: Simple, Elegant, Modern E-Commerce Standard -->
                        <template x-for="order in orders" :key="order.id">
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
                                        <h4 class="text-xs font-bold text-zinc-900 truncate" x-text="order.items && order.items.length > 0 ? (order.items[0]?.product_name || order.items[0]?.name || 'Produk Pesanan') : 'Produk Pesanan'"></h4>
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
