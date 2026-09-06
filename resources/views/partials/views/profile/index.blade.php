<!-- ========================================================= -->
            <div x-show="activeTab === 'profile' && !activeSubView" class="space-y-4">
                <!-- Header -->
                <div class="px-4 pt-3 pb-2 border-b border-zinc-200 flex items-center justify-between">
                    <h2 class="text-base font-extrabold text-zinc-950 tracking-tight" x-text="t('my_profile', 'Profil Akun')">Profil Akun</h2>
                    <span class="text-xs text-zinc-400" x-text="t('settings', 'Pengaturan')">Pengaturan</span>
                </div>

                <!-- 2 STRICT TABS: BIODATA DIRI & DAFTAR ALAMAT -->
                <div class="px-4">
                    <div class="grid grid-cols-2 bg-zinc-100 p-0.5 rounded-xl text-xs font-bold text-center border border-zinc-200/60">
                        <button 
                            @click="profileTab = 'biodata'" 
                            :class="profileTab === 'biodata' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-lg transition">
                            <span x-text="t('tab_biodata', 'Biodata Diri')">Biodata Diri</span>
                        </button>
                        <button 
                            @click="profileTab = 'alamat'; fetchAddresses()" 
                            :class="profileTab === 'alamat' ? 'bg-white text-zinc-950 shadow-sm' : 'text-zinc-500 hover:text-zinc-900'"
                            class="py-2 rounded-lg transition">
                            <span x-text="t('tab_addresses', 'Daftar Alamat')">Daftar Alamat</span>
                        </button>
                    </div>
                </div>

                <!-- SUB-TAB 1: BIODATA DIRI -->
                <div x-show="profileTab === 'biodata'" class="px-4 space-y-4">
                    <!-- Guest View -->
                    <template x-if="!isLoggedIn">
                        <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-4">
                            <div class="text-center space-y-1">
                                <h3 class="font-bold text-sm text-zinc-900" x-text="t('login_phone_title', 'Masuk dengan Nomor Handphone')">Masuk dengan Nomor Handphone</h3>
                                <p class="text-xs text-zinc-500" x-text="t('login_phone_desc', 'Verifikasi instan via kode OTP tanpa kata sandi rumit.')">Verifikasi instan via kode OTP tanpa kata sandi rumit.</p>
                            </div>

                            <!-- Fast 1-Click Demo Login -->
                            <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-xl space-y-2">
                                <span class="text-xs font-bold text-zinc-800 block" x-text="t('quick_dev_access', 'Akses Cepat Pengembang:')">Akses Cepat Pengembang:</span>
                                <button 
                                    @click="quickLoginDemo()" 
                                    :disabled="authLoading"
                                    class="w-full py-2.5 bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-xs rounded-lg transition disabled:opacity-50">
                                    <span x-show="!authLoading" x-text="t('quick_login_btn', 'Login sebagai Budi Santoso (081234567890)')">Login sebagai Budi Santoso (081234567890)</span>
                                    <span x-show="authLoading" x-text="t('processing_login', 'Memproses Login...')">Memproses Login...</span>
                                </button>
                            </div>

                            <!-- Standard OTP Form -->
                            <div class="border-t border-zinc-100 pt-3 space-y-3">
                                <template x-if="otpStep === 'phone'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-zinc-700" x-text="t('phone_input_label', 'Nomor Handphone (Indonesia)')">Nomor Handphone (Indonesia)</label>
                                        <input 
                                            type="tel" 
                                            x-model="authPhone" 
                                            placeholder="081234567890" 
                                            class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:bg-white focus:ring-1 focus:ring-zinc-900"
                                        >
                                        <button 
                                            @click="requestOtp()" 
                                            :disabled="authLoading"
                                            class="w-full py-2.5 bg-zinc-900 text-white font-bold text-xs rounded-lg hover:bg-zinc-800 transition" x-text="t('send_otp_btn', 'Kirim Kode OTP')">
                                            Kirim Kode OTP
                                        </button>
                                    </div>
                                </template>

                                <template x-if="otpStep === 'verify'">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <label class="block text-xs font-semibold text-zinc-700" x-text="t('enter_otp_label', 'Masukkan 6 Digit OTP')">Masukkan 6 Digit OTP</label>
                                            <button @click="otpStep = 'phone'" class="text-[11px] text-[#E60012] font-semibold" x-text="t('change_phone_btn', 'Ganti No HP')">Ganti No HP</button>
                                        </div>
                                        <input 
                                            type="text" 
                                            x-model="authOtp" 
                                            placeholder="123456" 
                                            maxlength="6"
                                            class="w-full px-3 py-2 text-center tracking-widest font-mono text-base font-bold bg-zinc-50 border border-zinc-200 rounded-lg focus:outline-none focus:bg-white focus:ring-1 focus:ring-zinc-900 tabular"
                                        >
                                        <p class="text-[10px] text-zinc-400" x-html="t('sandbox_otp_notice')">Kode OTP sandbox lokal: <strong>123456</strong></p>
                                        <button 
                                            @click="verifyOtp()" 
                                            :disabled="authLoading"
                                            class="w-full py-2.5 bg-[#E60012] text-white font-bold text-xs rounded-lg hover:bg-red-700 transition" x-text="t('verify_login_btn', 'Verifikasi & Masuk')">
                                            Verifikasi & Masuk
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Authenticated View -->
                    <template x-if="isLoggedIn && currentUser">
                        <div class="space-y-4">
                            <!-- Profile Header -->
                            <div class="bg-white border border-zinc-200 rounded-xl p-4 flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-zinc-900 text-white font-bold text-lg flex items-center justify-center">
                                    <span x-text="currentUser.name.substring(0, 1).toUpperCase()"></span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-sm text-zinc-900" x-text="currentUser.name"></h3>
                                    <p class="text-xs text-zinc-500 font-mono" x-text="currentUser.phone"></p>
                                    <span class="inline-block mt-1 px-2 py-0.2 bg-zinc-100 text-zinc-700 font-medium text-[10px] rounded border border-zinc-200" x-text="t('registered_member', 'Member Terdaftar')">
                                        Member Terdaftar
                                    </span>
                                </div>
                            </div>

                            <!-- Account Details -->
                            <div class="bg-white border border-zinc-200 rounded-xl p-4 space-y-3 text-xs">
                                <h4 class="font-bold text-zinc-900 border-b border-zinc-100 pb-2" x-text="t('biodata_info', 'Informasi Biodata')">Informasi Biodata</h4>
                                <div>
                                    <label class="text-zinc-500 block mb-1" x-text="t('user_name', 'Nama Lengkap')">Nama Lengkap</label>
                                    <input type="text" x-model="currentUser.name" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900">
                                </div>
                                <div>
                                    <label class="text-zinc-500 block mb-1" x-text="t('email_address', 'Email')">Email</label>
                                    <input type="email" x-model="currentUser.email" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900">
                                </div>
                                <div>
                                    <label class="text-zinc-500 block mb-1" x-text="t('locked_phone_label', 'Nomor Handphone (Terkunci)')">Nomor Handphone (Terkunci)</label>
                                    <input type="text" :value="currentUser.phone" disabled class="w-full px-3 py-2 bg-zinc-100 border border-zinc-200 rounded-lg text-zinc-400 font-mono">
                                </div>
                                <button @click="updateProfile()" class="w-full py-2 bg-zinc-900 text-white font-bold rounded-lg hover:bg-zinc-800 transition" x-text="t('save_changes', 'Simpan Perubahan')">
                                    Simpan Perubahan
                                </button>
                            </div>

                            <!-- Logout -->
                            <button @click="logout()" class="w-full py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-red-600 font-bold text-xs rounded-xl transition" x-text="t('sign_out', 'Keluar dari Akun (Logout)')">
                                Keluar dari Akun (Logout)
                            </button>
                        </div>
                    </template>
                </div>

                <!-- SUB-TAB 2: DAFTAR ALAMAT -->
                <div x-show="profileTab === 'alamat'" class="px-4 space-y-3 pb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-zinc-900" x-text="t('saved_addresses', 'Alamat Pengiriman Tersimpan')">Alamat Pengiriman Tersimpan</span>
                        <button @click="openAddressModal()" class="px-3 py-1 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-semibold rounded-lg">
                            <span x-text="t('add_new_address', '+ Tambah Alamat')">+ Tambah Alamat</span>
                        </button>
                    </div>

                    <template x-if="userAddresses.length === 0">
                        <div class="bg-white border border-zinc-200 rounded-xl p-8 text-center space-y-2">
                            <p class="text-xs font-bold text-zinc-800" x-text="t('no_address_saved', 'Belum Ada Alamat Tersimpan')">Belum Ada Alamat Tersimpan</p>
                            <p class="text-[11px] text-zinc-400" x-text="t('add_address_hint', 'Tambahkan alamat rumah atau kantor Anda untuk mempermudah proses checkout.')">Tambahkan alamat rumah atau kantor Anda untuk mempermudah proses checkout.</p>
                        </div>
                    </template>

                    <div class="space-y-2.5">
                        <template x-for="addr in userAddresses" :key="addr.id">
                            <div class="bg-white border border-zinc-200 rounded-xl p-3.5 space-y-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-zinc-900" x-text="addr.recipient_name"></span>
                                            <template x-if="addr.is_default">
                                                <span class="px-1.5 py-0.2 bg-zinc-100 text-zinc-800 text-[9px] font-bold rounded border border-zinc-200" x-text="t('primary_address_badge', 'Utama')">Utama</span>
                                            </template>
                                        </div>
                                        <p class="text-xs text-zinc-500 font-mono" x-text="addr.phone"></p>
                                    </div>
                                    <button @click="deleteAddress(addr.id)" class="text-zinc-400 hover:text-red-600 p-1">
                                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                                    </button>
                                </div>
                                <p class="text-xs text-zinc-700 leading-relaxed" x-text="addr.address"></p>
                                <p class="text-[11px] text-zinc-400" x-text="(addr.district ? addr.district + ', ' : '') + (addr.city || '') + ' ' + (addr.postal_code || '')"></p>

                                <template x-if="addr.delivery_note">
                                    <div class="inline-block text-[10px] bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded font-medium border border-zinc-200/50">
                                        <span x-text="t('instruction_label', 'Instruksi:')">Instruksi:</span> <span x-text="getDeliveryNoteLabel(addr.delivery_note)"></span>
                                    </div>
                                </template>

                                <template x-if="!addr.is_default">
                                    <div class="pt-2 border-t border-zinc-100">
                                        <button @click="setDefaultAddress(addr)" class="text-xs font-semibold text-[#E60012] hover:underline" x-text="t('set_as_default_address', 'Jadikan Alamat Utama')">
                                            Jadikan Alamat Utama
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
