<!-- ========================================================= -->
<!-- SUB-VIEW: SPESIAL UNTUK KAMU                              -->
<!-- ========================================================= -->
<div x-show="activeSubView === 'special-for-you'" class="bg-white min-h-screen space-y-4 pb-12" x-cloak>
    <!-- 1. Dedicated Top Sticky Header (Uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 py-3.5 flex items-center justify-between shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center gap-3">
            <button @click="closeSubView()" class="w-9 h-9 rounded-xl border border-white/30 bg-white/15 text-white flex items-center justify-center hover:bg-white/25 active:scale-95 transition shrink-0" :title="t('back_btn', 'Kembali')">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <span class="text-base font-extrabold text-white tracking-tight" x-text="t('special_for_you_title', 'Spesial Untuk Kamu')">Spesial Untuk Kamu</span>
        </div>
        <button @click="goToTab('cart')" class="relative p-1.5 text-white hover:text-white/80 transition cursor-pointer">
            <svg class="w-5 h-5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span x-show="cart && cart.total_qty > 0" class="absolute -top-1 -right-1 bg-[#00D06C] text-white font-bold text-[10px] min-w-[16px] h-4 rounded-full flex items-center justify-center px-1 tabular" x-text="cart.total_qty"></span>
        </button>
    </div>

    <!-- 2. Personalized Curated Hero Banner with Social Proof -->
    <div class="px-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1E1B4B] via-[#312E81] to-[#4338CA] text-white p-4 shadow-sm">
            <!-- Decorative Light Flare -->
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-400/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-2.5">
                <div class="inline-flex items-center gap-1.5 bg-amber-400/20 border border-amber-300/30 text-amber-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                    <svg class="w-3 h-3 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span x-text="t('curated_personal_special', 'Kurasi Personal Spesial')">Kurasi Personal Spesial</span>
                </div>

                <div>
                    <h2 class="text-lg font-black tracking-tight text-white leading-tight" x-text="t('best_choice_for_you', 'Pilihan Terbaik Buat Kamu')">Pilihan Terbaik Buat Kamu</h2>
                    <p class="text-xs text-indigo-100/90 mt-0.5" x-text="t('special_hero_desc', 'Direkomendasikan dari produk dengan tingkat kepuasan tertinggi')">Direkomendasikan dari produk dengan tingkat kepuasan tertinggi</p>
                </div>

                <!-- Social Proof Stats -->
                <div class="flex items-center gap-3 pt-1 border-t border-white/10">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span class="text-xs font-bold text-white" x-text="'4.9 / 5.0 ' + t('rating_label', 'Rating')">4.9 / 5.0 Rating</span>
                    </div>
                    <span class="text-white/40">•</span>
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-300 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span class="text-xs font-bold text-white" x-text="'98.4% ' + t('satisfied_label', 'Puas')">98.4% Puas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Subtabs: Semua Rekomendasi, Rating 4.9+, Trending, Paling Banyak Diulas -->
    <div class="px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1 text-xs">
            <button 
                @click="specialSubtab = 'all'" 
                :class="specialSubtab === 'all' ? 'bg-[#4338CA] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer">
                <span x-text="t('all_recommendations', 'Semua Rekomendasi')">Semua Rekomendasi</span>
            </button>
            <button 
                @click="specialSubtab = 'top-rated'" 
                :class="specialSubtab === 'top-rated' ? 'bg-[#4338CA] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <span x-text="t('rating_49_plus', 'Rating 4.9+')">Rating 4.9+</span>
            </button>
            <button 
                @click="specialSubtab = 'trending'" 
                :class="specialSubtab === 'trending' ? 'bg-[#4338CA] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                <span x-text="t('trending_this_week', 'Trending Pekan Ini')">Trending Pekan Ini</span>
            </button>
            <button 
                @click="specialSubtab = 'most-reviewed'" 
                :class="specialSubtab === 'most-reviewed' ? 'bg-[#4338CA] text-white font-bold shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 font-medium'"
                class="px-3.5 py-1.5 rounded-full transition shrink-0 cursor-pointer flex items-center gap-1">
                <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span x-text="t('most_reviewed', 'Banyak Diulas')">Banyak Diulas</span>
            </button>
        </div>
    </div>


    <!-- 4. Curated Special For You Products Grid -->
    <div class="px-4">
        <div class="grid grid-cols-2 gap-3">
            <template x-for="(prod, idx) in getSpecialForYouProducts()" :key="'sfy-view-' + prod.id">
                <div class="bg-white border border-indigo-100 rounded-2xl p-2.5 shadow-xs flex flex-col justify-between hover:border-indigo-300 transition group">
                    <div>
                        <!-- Product Image + Badge (HOT PICK / STAFF PICK) -->
                        <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-zinc-50 cursor-pointer" @click="openProductDetail(prod)">
                            <img :src="prod.primary_image || getFallbackImage(prod)" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.src='https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80'">
                            
                            <!-- Distinctive Dynamic Badge -->
                            <span 
                                :class="idx % 3 === 0 ? 'bg-orange-600' : (idx % 3 === 1 ? 'bg-indigo-600' : 'bg-blue-600')"
                                class="absolute top-2 left-2 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-2xs tracking-wider"
                                x-text="idx % 3 === 0 ? 'HOT PICK' : (idx % 3 === 1 ? 'STAFF PICK' : 'TRENDING')">
                            </span>

                            <!-- Star Rating Pill -->
                            <div class="absolute bottom-2 left-2 bg-white/95 backdrop-blur-xs border border-zinc-200 text-zinc-900 text-[10px] font-bold px-1.5 py-0.5 rounded-md flex items-center gap-1 shadow-2xs">
                                <svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <span>4.9</span>
                                <span class="text-zinc-400 text-[9px]" x-text="'(' + (80 + idx * 24) + ')'"></span>
                            </div>
                        </div>

                        <!-- Product Title -->
                        <h4 class="text-xs font-semibold text-zinc-900 line-clamp-2 mt-2 cursor-pointer hover:text-[#4338CA] leading-tight" @click="openProductDetail(prod)" x-text="prod.name"></h4>

                        <!-- Price -->
                        <div class="mt-1.5">
                            <span class="text-[#00A862] font-black text-sm tabular block leading-tight" x-text="formatRupiah(prod.final_price)"></span>
                        </div>

                        <!-- Customer Review Snippet -->
                        <div class="mt-2 p-1.5 bg-zinc-50 border border-zinc-100 rounded-lg text-[10px] text-zinc-600 leading-tight italic">
                            <span x-text="idx % 2 === 0 ? t('review_snippet_1', '“Rasa otentik, packing aman sampai Tokyo!”') : t('review_snippet_2', '“Bumbu mantap, sangat mengobati rindu kampung halaman.”')"></span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3 pt-2 border-t border-zinc-100">
                        <template x-if="getCartItemQty(prod.id) === 0">
                            <button 
                                @click="addToCart(prod, 1)" 
                                class="w-full py-2 bg-[#4338CA] hover:bg-[#3730A3] text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer active:scale-98">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span x-text="t('add_to_cart_short', '+ Keranjang')">+ Keranjang</span>
                            </button>
                        </template>
                        <template x-if="getCartItemQty(prod.id) > 0">
                            <div class="flex items-center justify-between w-full bg-indigo-50 border border-indigo-200 rounded-xl p-1">
                                <button @click="changeCartQty(prod.id, -1)" class="w-7 h-7 bg-white border border-indigo-200 rounded-lg text-indigo-700 font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">-</button>
                                <span class="text-xs font-bold text-indigo-800 tabular" x-text="getCartItemQty(prod.id)"></span>
                                <button @click="changeCartQty(prod.id, 1)" class="w-7 h-7 bg-[#4338CA] rounded-lg text-white font-bold text-xs cursor-pointer active:scale-95 flex items-center justify-center">+</button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
