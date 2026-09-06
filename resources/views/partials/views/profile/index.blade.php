<!-- ========================================================= -->
<!-- VIEW 4: PROFIL SAYA (REFERENCE DESIGN IMPLEMENTATION)      -->
<!-- Matching media_1788727527247.png & media_1788727535492.png  -->
<!-- ========================================================= -->
<div x-show="activeTab === 'profile' && !activeSubView" class="bg-white min-h-screen">
    
    <!-- 1. Dedicated Top Header (Matching uniform Royal Blue #1657FF) -->
    <div class="sticky top-0 z-30 bg-[#1657FF] text-white px-4 pt-3.5 pb-0 shadow-xs -mx-px w-[calc(100%+2px)]">
        <div class="flex items-center justify-between mb-2.5">
            <h1 class="text-base font-extrabold text-white tracking-tight" x-text="t('my_profile', 'Profil Saya')">Profil Saya</h1>
        </div>

        <!-- 2 STRICT TABS: BIODATA DIRI & DAFTAR ALAMAT -->
        <div class="grid grid-cols-2 text-center text-sm font-bold">
            <button 
                @click="profileTab = 'biodata'" 
                :class="profileTab === 'biodata' ? 'text-white' : 'text-white/70 hover:text-white'"
                class="pb-2.5 relative transition cursor-pointer">
                <span x-text="t('tab_biodata', 'Biodata Diri')">Biodata Diri</span>
                <template x-if="profileTab === 'biodata'">
                    <div class="absolute bottom-0 inset-x-2 h-0.5 bg-[#00D06C] rounded-full"></div>
                </template>
            </button>
            <button 
                @click="profileTab = 'alamat'; fetchAddresses()" 
                :class="profileTab === 'alamat' ? 'text-white' : 'text-white/70 hover:text-white'"
                class="pb-2.5 relative transition cursor-pointer">
                <span x-text="t('tab_addresses', 'Daftar Alamat')">Daftar Alamat</span>
                <template x-if="profileTab === 'alamat'">
                    <div class="absolute bottom-0 inset-x-2 h-0.5 bg-[#00D06C] rounded-full"></div>
                </template>
            </button>
        </div>
    </div>

    <!-- SUB-TAB 1: BIODATA DIRI (Matching media_1788727527247.png) -->
    <div x-show="profileTab === 'biodata'">
        <!-- Guest View (If Not Logged In) -->
        <template x-if="!isLoggedIn">
            <div class="p-6 space-y-4">
                <div class="bg-white border border-zinc-200/90 rounded-2xl p-6 space-y-4 shadow-2xs text-center">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-[#1657FF] flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-sm text-zinc-900" x-text="t('login_phone_title', 'Masuk dengan Nomor Handphone')">Masuk dengan Nomor Handphone</h3>
                        <p class="text-xs text-zinc-500" x-text="t('login_phone_desc', 'Verifikasi instan via kode OTP tanpa kata sandi rumit.')">Verifikasi instan via kode OTP tanpa kata sandi rumit.</p>
                    </div>

                    <!-- Fast 1-Click Demo Login -->
                    <div class="p-3 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-2 text-left">
                        <span class="text-xs font-bold text-zinc-800 block" x-text="t('quick_dev_access', 'Akses Cepat Pengembang:')">Akses Cepat Pengembang:</span>
                        <button 
                            @click="quickLoginDemo()" 
                            :disabled="authLoading"
                            class="w-full py-2.5 bg-[#1657FF] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition disabled:opacity-50 shadow-2xs cursor-pointer">
                            <span x-show="!authLoading" x-text="t('quick_login_btn', 'Login sebagai Budi Santoso (081234567890)')">Login sebagai Budi Santoso (081234567890)</span>
                            <span x-show="authLoading" x-text="t('processing_login', 'Memproses Login...')">Memproses Login...</span>
                        </button>
                    </div>

                    <!-- Standard OTP Form -->
                    <div class="border-t border-zinc-100 pt-3 space-y-3 text-left">
                        <template x-if="otpStep === 'phone'">
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-zinc-700" x-text="t('phone_input_label', 'Nomor Handphone (Indonesia)')">Nomor Handphone (Indonesia)</label>
                                <input 
                                    type="tel" 
                                    x-model="authPhone" 
                                    placeholder="081234567890" 
                                    class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-600"
                                >
                                <button 
                                    @click="requestOtp()" 
                                    :disabled="authLoading"
                                    class="w-full py-2.5 bg-zinc-900 text-white font-bold text-xs rounded-lg hover:bg-zinc-800 transition cursor-pointer" x-text="t('send_otp_btn', 'Kirim Kode OTP')">
                                    Kirim Kode OTP
                                </button>
                            </div>
                        </template>

                        <template x-if="otpStep === 'verify'">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-xs font-semibold text-zinc-700" x-text="t('enter_otp_label', 'Masukkan 6 Digit OTP')">Masukkan 6 Digit OTP</label>
                                    <button @click="otpStep = 'phone'" class="text-[11px] text-[#1657FF] font-semibold cursor-pointer" x-text="t('change_phone_btn', 'Ganti No HP')">Ganti No HP</button>
                                </div>
                                <input 
                                    type="text" 
                                    x-model="authOtp" 
                                    placeholder="123456" 
                                    maxlength="6"
                                    class="w-full px-3 py-2 text-center tracking-widest font-mono text-base font-bold bg-zinc-50 border border-zinc-200 rounded-lg focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-600 tabular"
                                >
                                <p class="text-[10px] text-zinc-400" x-html="t('sandbox_otp_notice')">Kode OTP sandbox lokal: <strong>123456</strong></p>
                                <button 
                                    @click="verifyOtp()" 
                                    :disabled="authLoading"
                                    class="w-full py-2.5 bg-[#00D06C] hover:bg-[#00B85F] text-white font-bold text-xs rounded-lg transition cursor-pointer" x-text="t('verify_login_btn', 'Verifikasi & Masuk')">
                                    Verifikasi & Masuk
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Authenticated View (Matching media_1788727527247.png) -->
        <template x-if="isLoggedIn && currentUser">
            <div>
                <!-- Blue Hero Area with Circular Sky Avatar and Edit Badge -->
                <div class="bg-[#1657FF] pt-4 pb-8 px-4 text-center">
                    <!-- Circular Avatar with Green Pencil Edit Badge -->
                    <div class="relative w-20 h-20 rounded-full border-2 border-white/80 overflow-visible mx-auto shadow-md">
                        <img 
                            src="https://images.unsplash.com/photo-1534447677768-be436bb09401?w=400&fit=crop&q=80" 
                            alt="Avatar Profil" 
                            class="w-full h-full object-cover rounded-full">
                        <!-- Green Pencil Edit Badge -->
                        <button 
                            @click="openEditProfileField('name', 'Nama Lengkap', currentUser?.name)"
                            class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-[#00D06C] border-2 border-[#1657FF] flex items-center justify-center text-white cursor-pointer shadow-2xs hover:scale-105 active:scale-95 transition"
                            title="Ubah Foto Profil">
                            <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- User Name & Email -->
                    <h2 class="font-extrabold text-base text-white mt-2.5 tracking-tight" x-text="currentUser?.name || 'Budi Santoso'">Budi Santoso</h2>
                    <p class="text-xs text-white/80 font-medium mt-0.5" x-text="currentUser?.email || 'budi.santoso@email.com'">budi.santoso@email.com</p>
                </div>

                <!-- White Card Container with Rounded Top Corners -->
                <div class="bg-white rounded-t-[32px] -mt-5 pt-6 pb-28 px-5 space-y-3.5 shadow-md min-h-[460px]">
                    
                    <!-- FIELD 1: NAMA LENGKAP -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1">NAMA LENGKAP</label>
                        <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                            <span class="text-xs font-bold text-zinc-900 flex-1 truncate" x-text="currentUser?.name || 'Budi Santoso'"></span>
                            <button @click="openEditProfileField('name', 'Nama Lengkap', currentUser?.name)" class="text-zinc-400 hover:text-zinc-700 transition p-1" title="Ubah Nama">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- FIELD 2: ALAMAT EMAIL -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1">ALAMAT EMAIL</label>
                        <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                            <span class="text-xs font-medium text-zinc-800 flex-1 truncate" x-text="currentUser?.email || 'budi.santoso@email.com'"></span>
                            <button @click="openEditProfileField('email', 'Alamat Email', currentUser?.email)" class="text-zinc-400 hover:text-zinc-700 transition p-1" title="Ubah Email">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- FIELD 3: NO. WHATSAPP AKTIF -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1">NO. WHATSAPP AKTIF</label>
                        <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                            <span class="text-xs font-medium text-zinc-800 flex-1 truncate font-mono" x-text="currentUser?.phone ? ('+' + currentUser.phone.replace(/^\+/, '')) : '+62 812-3456-7890'"></span>
                            <button @click="openEditProfileField('phone', 'No. WhatsApp Aktif', currentUser?.phone)" class="text-zinc-400 hover:text-zinc-700 transition p-1" title="Ubah Nomor WhatsApp">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- FIELD 4: NO. KTP / ID -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1">NO. KTP / ID</label>
                        <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                            <span class="text-xs font-medium text-zinc-800 flex-1 truncate font-mono" x-text="profileKtp || '3171012505870003'"></span>
                            <button @click="openEditProfileField('identity_number', 'No. KTP / ID', profileKtp)" class="text-zinc-400 hover:text-zinc-700 transition p-1" title="Ubah Nomor KTP">
                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- ACTION BUTTON 1: GANTI PASSWORD -->
                    <div class="pt-1">
                        <button 
                            @click="changePasswordPrompt()" 
                            class="bg-zinc-50/70 border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between w-full hover:bg-zinc-100 transition cursor-pointer shadow-2xs active:scale-[0.99]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100/90 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <span class="font-bold text-xs text-zinc-900" x-text="t('change_password', 'Ganti Password')">Ganti Password</span>
                            </div>
                            <svg class="w-4 h-4 text-zinc-400 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>

                    <!-- ACTION BUTTON 2: KELUAR AKUN -->
                    <div class="pt-1">
                        <button 
                            @click="logout()" 
                            class="bg-rose-50/80 border border-rose-200/70 rounded-2xl py-3.5 px-4 flex items-center justify-center gap-2 w-full text-rose-500 hover:bg-rose-100 transition cursor-pointer font-bold text-xs shadow-2xs active:scale-[0.99]">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span x-text="t('sign_out', 'Keluar Akun')">Keluar Akun</span>
                        </button>
                    </div>

                </div>
            </div>
        </template>
    </div>

    <!-- SUB-TAB 2: DAFTAR ALAMAT (Matching media_1788725880610.png / media_1788727535492.png) -->
    <div x-show="profileTab === 'alamat'" class="bg-zinc-50/50 min-h-screen px-4 py-4 space-y-3 pb-28">
        
        <!-- Search Input Bar (Rounded Pill with Magnifying Glass) -->
        <div class="bg-white border border-zinc-200 rounded-full px-4 py-2.5 flex items-center gap-2.5 shadow-2xs">
            <svg class="w-4 h-4 text-zinc-400 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
                type="text" 
                x-model="addressSearchQuery" 
                placeholder="Cari alamat..." 
                class="w-full bg-transparent text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none">
            <button x-show="addressSearchQuery" @click="addressSearchQuery = ''" class="text-zinc-400 hover:text-zinc-600">
                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Address List Loop -->
        <template x-if="getFilteredAddresses().length === 0">
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-8 text-center space-y-2 shadow-2xs">
                <p class="text-xs font-bold text-zinc-800" x-text="t('no_address_saved', 'Belum Ada Alamat Ditemukan')">Belum Ada Alamat Ditemukan</p>
                <p class="text-[11px] text-zinc-400" x-text="t('add_address_hint', 'Tambahkan alamat pengiriman Anda untuk kemudahan checkout.')">Tambahkan alamat pengiriman Anda untuk kemudahan checkout.</p>
            </div>
        </template>

        <template x-for="addr in getFilteredAddresses()" :key="addr.id">
            <div class="bg-white border border-zinc-200/90 rounded-2xl p-4 shadow-2xs space-y-2">
                <!-- Badges & Action Buttons -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span 
                            class="px-3 py-0.5 bg-blue-50 text-[#1657FF] text-[11px] font-extrabold rounded-full"
                            x-text="addr.address?.toLowerCase().includes('thamrin') || addr.address?.toLowerCase().includes('kantor') ? 'Kantor' : 'Rumah'">
                        </span>
                        <template x-if="addr.is_default">
                            <span class="px-3 py-0.5 bg-emerald-50 text-emerald-600 text-[11px] font-extrabold rounded-full" x-text="t('primary_address_badge', 'Utama')">Utama</span>
                        </template>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <!-- Edit Button (Light Blue Rounded Square with Pencil SVG) -->
                        <button 
                            @click="openAddressModal(addr)" 
                            class="w-7 h-7 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#1657FF] flex items-center justify-center transition cursor-pointer shadow-2xs" 
                            title="Ubah Alamat">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                            </svg>
                        </button>
                        <!-- Delete Button (Soft Red Rounded Square with X SVG, for non-default) -->
                        <template x-if="!addr.is_default">
                            <button 
                                @click="deleteAddress(addr.id)" 
                                class="w-7 h-7 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-500 flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                title="Hapus Alamat">
                                <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Recipient Name, Address, and Phone Number -->
                <h4 class="font-bold text-xs text-zinc-900 mt-1" x-text="addr.recipient_name"></h4>
                <p class="text-xs text-zinc-500 leading-relaxed" x-text="addr.address + ', ' + (addr.district ? addr.district + ', ' : '') + (addr.city || '') + (addr.postal_code ? ' ' + addr.postal_code : '')"></p>
                <p class="text-xs font-semibold text-[#1657FF] font-mono mt-0.5" x-text="'+' + addr.phone.replace(/^\+/, '')"></p>

                <!-- Set Default Button if not default -->
                <template x-if="!addr.is_default">
                    <div class="pt-1.5 border-t border-zinc-100 flex justify-end">
                        <button @click="setDefaultAddress(addr)" class="text-[11px] font-bold text-[#1657FF] hover:underline cursor-pointer" x-text="t('set_as_default_address', 'Jadikan Alamat Utama')">
                            Jadikan Alamat Utama
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <!-- Tambah Alamat Baru Button (Dashed Blue Capsule Button) -->
        <button 
            @click="openAddressModal()" 
            class="border-2 border-dashed border-blue-400 bg-blue-50/40 hover:bg-blue-50/80 text-[#1657FF] font-bold text-xs py-3.5 px-4 rounded-2xl flex items-center justify-center gap-2 w-full transition shadow-2xs cursor-pointer active:scale-[0.99]">
            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span x-text="t('add_new_address', 'Tambah Alamat Baru')">Tambah Alamat Baru</span>
        </button>

    </div>

    <!-- MODAL: EDIT PROFILE FIELD (NAME, EMAIL, PHONE, KTP) -->
    <div x-show="profileEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs" x-transition>
        <div @click.away="profileEditModal = false" class="bg-white w-full max-w-sm rounded-3xl p-5 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-sm text-zinc-900" x-text="'Ubah ' + profileEditLabel"></h3>
                <button @click="profileEditModal = false" class="text-zinc-400 hover:text-zinc-600 p-1">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div>
                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-1" x-text="profileEditLabel"></label>
                <input 
                    type="text" 
                    x-model="profileEditValue" 
                    @keydown.enter="saveProfileField()"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-zinc-200 text-xs text-zinc-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2 pt-1">
                <button @click="profileEditModal = false" class="px-4 py-2 rounded-xl text-zinc-600 hover:bg-zinc-100 text-xs font-semibold cursor-pointer">Batal</button>
                <button @click="saveProfileField()" class="px-5 py-2 bg-[#1657FF] hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-2xs cursor-pointer">Simpan</button>
            </div>
        </div>
    </div>

</div>
