<!-- ========================================================= -->
            <!-- SUB-VIEW: BRAND / KATEGORI FILTERED PAGE                  -->
            <!-- ========================================================= -->
            <div x-show="activeSubView === 'brand-category'" class="space-y-4">
                <!-- Sub-header -->
                <div class="bg-zinc-50 px-4 py-3 border-b border-zinc-200 flex items-center gap-3">
                    <button @click="closeSubView()" class="w-8 h-8 rounded-lg bg-white border border-zinc-200 text-zinc-700 flex items-center justify-center text-xs hover:bg-zinc-100 transition">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <div>
                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Koleksi Jastip</span>
                        <h2 class="text-sm font-bold text-zinc-900" x-text="'Aneka Produk ' + selectedFilter.name"></h2>
                    </div>
                </div>

                <!-- Subtabs: Semua, Terlaris, Termurah, Promo -->
                <div class="px-4">
                    <div class="flex gap-1.5 overflow-x-auto no-scrollbar">
                        <button 
                            @click="selectedFilter.subtab = 'all'" 
                            :class="selectedFilter.subtab === 'all' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700'"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition" x-text="t('subtab_all', 'Semua')">
                            Semua
                        </button>
                        <button 
                            @click="selectedFilter.subtab = 'bestseller'" 
                            :class="selectedFilter.subtab === 'bestseller' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700'"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition" x-text="t('curated_bestseller', 'Terlaris')">
                            Terlaris
                        </button>
                        <button 
                            @click="selectedFilter.subtab = 'cheapest'" 
                            :class="selectedFilter.subtab === 'cheapest' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700'"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition" x-text="t('subtab_cheapest', 'Termurah')">
                            Termurah
                        </button>
                        <button 
                            @click="selectedFilter.subtab = 'promo'" 
                            :class="selectedFilter.subtab === 'promo' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700'"
                            class="px-3 py-1 rounded-lg text-xs font-medium transition" x-text="t('subtab_promo', 'Promo')">
                            Promo
                        </button>
                    </div>
                </div>

                <!-- Filtered Products Grid -->
                <div class="px-4">
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="prod in getFilteredSubViewProducts()" :key="prod.id">
                            <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden flex flex-col justify-between">
                                <div class="relative w-full aspect-square bg-zinc-50 cursor-pointer" @click="openProductDetail(prod)">
                                    <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                                    <span 
                                        :class="prod.availability_type === 'ready_stock' ? 'bg-emerald-700 text-white' : 'bg-zinc-900 text-white'"
                                        class="absolute top-1.5 right-1.5 text-[9px] font-bold px-1.5 py-0.5 rounded font-mono" 
                                        x-text="prod.availability_type === 'ready_stock' ? 'READY' : 'PO'">
                                    </span>
                                </div>
                                <div class="p-2.5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 cursor-pointer hover:text-[#E60012]" @click="openProductDetail(prod)" x-text="prod.name"></h4>
                                        <span class="text-xs font-bold text-zinc-950 block mt-1 tabular" x-text="formatRupiah(prod.final_price)"></span>
                                    </div>
                                    <div class="mt-2.5 pt-2 border-t border-zinc-100">
                                        <template x-if="getCartItemQty(prod.id) === 0">
                                            <button @click="addToCart(prod, 1)" class="w-full py-1.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition" x-text="t('add_to_cart_short', '+ Keranjang')">
                                                + Keranjang
                                            </button>
                                        </template>
                                        <template x-if="getCartItemQty(prod.id) > 0">
                                            <div class="flex items-center justify-between w-full bg-zinc-50 border border-zinc-200 rounded-lg p-0.5">
                                                <button @click="changeCartQty(prod.id, -1)" class="w-6 h-6 bg-white border border-zinc-200 rounded text-zinc-800 font-bold text-xs">-</button>
                                                <span class="text-xs font-bold text-zinc-900 tabular" x-text="getCartItemQty(prod.id)"></span>
                                                <button @click="changeCartQty(prod.id, 1)" class="w-6 h-6 bg-[#E60012] rounded text-white font-bold text-xs">+</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
