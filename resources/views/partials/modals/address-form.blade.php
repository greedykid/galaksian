<!-- ADDRESS FORM MODAL -->
        <div x-show="showAddressModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" @click.self="showAddressModal = false">
            <div class="w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-xl flex flex-col">
                <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex justify-between items-center">
                    <h3 class="font-bold text-xs text-zinc-900" x-text="t('add_new_address', 'Tambah Alamat Baru')">Tambah Alamat Baru</h3>
                    <button @click="showAddressModal = false" class="text-zinc-400 hover:text-zinc-600 font-bold text-xs">✕</button>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1" x-text="t('recipient_name', 'Nama Penerima')">Nama Penerima</label>
                        <input type="text" x-model="addressForm.recipient_name" placeholder="Nama Lengkap" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1" x-text="t('phone_number', 'Nomor Handphone')">Nomor Handphone</label>
                        <input type="tel" x-model="addressForm.phone" placeholder="081234567890" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1" x-text="t('full_address', 'Alamat Lengkap')">Alamat Lengkap</label>
                        <textarea x-model="addressForm.address" rows="2" placeholder="Nama jalan, gedung, RT/RW" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1" x-text="t('city', 'Kota')">Kota</label>
                            <input type="text" x-model="addressForm.city" placeholder="Jakarta Selatan" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900">
                        </div>
                        <div>
                            <label class="block font-semibold text-zinc-700 mb-1" x-text="t('postal_code', 'Kode Pos')">Kode Pos</label>
                            <input type="text" x-model="addressForm.postal_code" placeholder="12190" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-zinc-700 mb-1" x-text="t('courier_note', 'Catatan Kurir')">Catatan Kurir</label>
                        <select x-model="addressForm.delivery_note" class="w-full px-3 py-1.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-zinc-900">
                            <option value="leave_at_front_door" x-text="t('delivery_leave_door', 'Taruh di depan pintu')">Taruh di depan pintu</option>
                            <option value="contact_before_delivery" x-text="t('delivery_call_before', 'Hubungi sebelum antar')">Hubungi sebelum antar</option>
                            <option value="hand_to_receiver" x-text="t('delivery_hand_receiver', 'Serahkan langsung ke penerima')">Serahkan langsung ke penerima</option>
                            <option value="security_desk" x-text="t('delivery_security_desk', 'Titip di pos satpam')">Titip di pos satpam</option>
                            <option value="other" x-text="t('delivery_other', 'Lainnya')">Lainnya</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2 pt-1">
                        <input type="checkbox" x-model="addressForm.is_default" class="text-zinc-900 rounded">
                        <span class="text-zinc-700 font-medium text-xs" x-text="t('set_default_address', 'Jadikan Alamat Utama')">Jadikan Alamat Utama</span>
                    </label>

                    <button @click="saveAddress()" class="w-full mt-2 py-2.5 bg-zinc-950 text-white font-bold rounded-lg hover:bg-zinc-800 transition" x-text="t('save_address', 'Simpan Alamat')">
                        Simpan Alamat
                    </button>
                </div>
            </div>
        </div>
