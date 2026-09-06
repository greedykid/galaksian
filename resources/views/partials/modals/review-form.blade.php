<!-- REVIEW BOTTOM SHEET MODAL -->
<div x-show="showReviewModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:leave="transition ease-in duration-250"
     x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center overflow-hidden" 
     style="display: none;"
     @keydown.window.escape="showReviewModal = false">

    <!-- Smooth Backdrop Fade -->
    <div x-show="showReviewModal"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-250"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-[2px]" 
         @click="showReviewModal = false">
    </div>

    <!-- Sliding Bottom Sheet Panel -->
    <div x-show="showReviewModal"
         x-transition:enter="transition-transform ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-transform ease-in duration-250 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         :style="getSheetStyle('review')"
         class="relative w-full max-w-[430px] bg-white rounded-t-[28px] max-h-[85vh] overflow-hidden flex flex-col shadow-2xl z-10 border-t border-zinc-100">

        <!-- Grab Bar Handle (Swipeable Up/Down when pressed) -->
        <div class="pt-3 pb-1.5 flex flex-col items-center justify-center cursor-grab active:cursor-grabbing select-none touch-none w-full"
             @touchstart.passive="startSheetDrag('review', $event)"
             @mousedown="startSheetDrag('review', $event)">
            <div class="w-12 h-1.5 bg-zinc-300 rounded-full hover:bg-zinc-400 active:bg-zinc-500 transition-colors"></div>
        </div>

        <!-- Modal Header -->
        <div class="px-4 py-2.5 border-b border-zinc-100 flex justify-between items-center bg-white cursor-grab active:cursor-grabbing select-none"
             @touchstart.passive="startSheetDrag('review', $event)"
             @mousedown="startSheetDrag('review', $event)">
            <h3 class="font-bold text-xs text-zinc-900" x-text="t('review_product', 'Ulas Produk')">Ulas Produk</h3>
            <button @click.stop="showReviewModal = false" 
                    type="button"
                    class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Review Content (Scrollable) -->
        <div class="p-4 space-y-3.5 text-xs overflow-y-auto flex-1">
            <!-- Star Rating -->
            <div class="text-center space-y-1">
                <p class="text-[11px] text-zinc-500 font-medium">Beri penilaian pengalaman jastip Anda:</p>
                <div class="flex items-center justify-center gap-2 py-1">
                    <template x-for="star in [1,2,3,4,5]" :key="star">
                        <button @click="reviewForm.rating = star" 
                                type="button"
                                class="p-1 text-zinc-300 hover:text-amber-500 transition-transform active:scale-110" 
                                :class="star <= reviewForm.rating ? 'text-amber-400' : 'text-zinc-200'">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <textarea 
                x-model="reviewForm.comment" 
                rows="3" 
                :placeholder="t('review_placeholder', 'Tulis ulasan Anda mengenai kualitas barang dan layanan jastip...')" 
                class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] transition text-xs"
            ></textarea>

            <button @click="submitReview()" 
                    type="button"
                    class="w-full py-3 bg-[#1657FF] text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md shadow-blue-500/20 active:scale-98" 
                    x-text="t('submit_review', 'Kirim Ulasan')">
                Kirim Ulasan
            </button>
        </div>
    </div>
</div>
