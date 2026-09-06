<!-- REVIEW MODAL -->
        <div x-show="showReviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" @click.self="showReviewModal = false">
            <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-xl p-4 space-y-3 text-xs">
                <div class="flex justify-between items-center border-b border-zinc-100 pb-2">
                    <h3 class="font-bold text-sm text-zinc-900" x-text="t('review_product', 'Ulas Produk')">Ulas Produk</h3>
                    <button @click="showReviewModal = false" class="text-zinc-400 font-bold">✕</button>
                </div>
                <!-- Stars -->
                <div class="flex items-center justify-center gap-2 py-2">
                    <template x-for="star in [1,2,3,4,5]" :key="star">
                        <button @click="reviewForm.rating = star" class="text-zinc-300 hover:text-amber-500 transition" :class="star <= reviewForm.rating ? 'text-amber-500' : 'text-zinc-300'">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </button>
                    </template>
                </div>
                <textarea 
                    x-model="reviewForm.comment" 
                    rows="3" 
                    :placeholder="t('review_placeholder', 'Tulis ulasan Anda mengenai kualitas barang dan layanan jastip...')" 
                    class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900"
                ></textarea>
                <button @click="submitReview()" class="w-full py-2.5 bg-zinc-950 text-white font-bold rounded-lg hover:bg-zinc-800 transition" x-text="t('submit_review', 'Kirim Ulasan')">
                    Kirim Ulasan
                </button>
            </div>
        </div>
