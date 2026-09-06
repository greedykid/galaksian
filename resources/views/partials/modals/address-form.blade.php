<!-- ADDRESS FORM BOTTOM SHEET MODAL -->
<div x-show="showAddressModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:leave="transition ease-in duration-250"
     x-cloak
     class="fixed inset-0 z-50 flex items-end justify-center overflow-hidden" 
     style="display: none;"
     @keydown.window.escape="showAddressModal = false">

    <!-- Smooth Backdrop Fade -->
    <div x-show="showAddressModal"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-250"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-[2px]" 
         @click="showAddressModal = false">
    </div>

    <!-- Sliding Bottom Sheet Panel -->
    <div x-show="showAddressModal"
         x-transition:enter="transition-transform ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-transform ease-in duration-250 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         :style="getSheetStyle('address')"
         class="relative w-full max-w-[430px] bg-white rounded-t-[28px] max-h-[88vh] overflow-hidden flex flex-col shadow-2xl z-10 border-t border-zinc-100">

        <!-- Grab Bar Handle (Swipeable Up/Down when pressed) -->
        <div class="pt-3 pb-1.5 flex flex-col items-center justify-center cursor-grab active:cursor-grabbing select-none touch-none w-full"
             @touchstart.passive="startSheetDrag('address', $event)"
             @mousedown="startSheetDrag('address', $event)">
            <div class="w-12 h-1.5 bg-zinc-300 rounded-full hover:bg-zinc-400 active:bg-zinc-500 transition-colors"></div>
        </div>

        <!-- Modal Header -->
        <div class="px-4 py-2.5 border-b border-zinc-100 flex justify-between items-center bg-white cursor-grab active:cursor-grabbing select-none"
             @touchstart.passive="startSheetDrag('address', $event)"
             @mousedown="startSheetDrag('address', $event)">
            <h3 class="font-bold text-xs text-zinc-900" x-text="t('add_new_address', 'Tambah Alamat Baru')">Tambah Alamat Baru</h3>
            <button @click.stop="showAddressModal = false" 
                    type="button"
                    class="w-7 h-7 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 flex items-center justify-center transition-colors shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form Content (Scrollable) -->
        <div class="p-4 space-y-3 text-xs overflow-y-auto flex-1">
            <div>
                <label class="block font-semibold text-zinc-700 mb-1" x-text="t('recipient_name', 'Nama Penerima')">Nama Penerima</label>
                <input type="text" x-model="addressForm.recipient_name" placeholder="Nama Lengkap" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] transition">
            </div>
            <div>
                <label class="block font-semibold text-zinc-700 mb-1" x-text="t('phone_number', 'Nomor Handphone')">Nomor Handphone</label>
                <input type="tel" x-model="addressForm.phone" placeholder="081234567890" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] font-mono transition">
            </div>
            <div>
                <label class="block font-semibold text-zinc-700 mb-1" x-text="t('full_address', 'Alamat Lengkap')">Alamat Lengkap</label>
                <textarea x-model="addressForm.address" rows="2" placeholder="Nama jalan, gedung, RT/RW" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] transition"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-zinc-700 mb-1" x-text="t('city', 'Kota')">Kota</label>
                    <input type="text" x-model="addressForm.city" placeholder="Jakarta Selatan" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] transition">
                </div>
                <div>
                    <label class="block font-semibold text-zinc-700 mb-1" x-text="t('postal_code', 'Kode Pos')">Kode Pos</label>
                    <input type="text" x-model="addressForm.postal_code" placeholder="12190" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] font-mono transition">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-zinc-700 mb-1" x-text="t('courier_note', 'Catatan Kurir')">Catatan Kurir</label>
                <select x-model="addressForm.delivery_note" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-[#1657FF] focus:border-[#1657FF] transition">
                    <option value="leave_at_front_door" x-text="t('delivery_leave_door', 'Taruh di depan pintu')">Taruh di depan pintu</option>
                    <option value="contact_before_delivery" x-text="t('delivery_call_before', 'Hubungi sebelum antar')">Hubungi sebelum antar</option>
                    <option value="hand_to_receiver" x-text="t('delivery_hand_receiver', 'Serahkan langsung ke penerima')">Serahkan langsung ke penerima</option>
                    <option value="security_desk" x-text="t('delivery_security_desk', 'Titip di pos satpam')">Titip di pos satpam</option>
                    <option value="other" x-text="t('delivery_other', 'Lainnya')">Lainnya</option>
                </select>
            </div>
            <label class="flex items-center gap-2 pt-1 cursor-pointer select-none">
                <input type="checkbox" x-model="addressForm.is_default" class="text-[#1657FF] rounded focus:ring-[#1657FF]">
                <span class="text-zinc-700 font-medium text-xs" x-text="t('set_default_address', 'Jadikan Alamat Utama')">Jadikan Alamat Utama</span>
            </label>

            <button @click="saveAddress()" 
                    type="button"
                    class="w-full mt-3 py-3 bg-[#1657FF] text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-md shadow-blue-500/20 active:scale-98" 
                    x-text="t('save_address', 'Simpan Alamat')">
                Simpan Alamat
            </button>
        </div>
    </div>
</div>
