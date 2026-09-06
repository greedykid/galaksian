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
                <!-- Blue Hero Area with Circular Sky Avatar and Edit Badge (Edge-to-edge flush) -->
                <div class="bg-[#1657FF] pt-4 pb-8 px-4 text-center -mx-px w-[calc(100%+2px)]">
                    <!-- Circular Avatar with Green Pencil Edit Badge -->
                    <div class="relative w-20 h-20 rounded-full border-2 border-white/80 overflow-visible mx-auto shadow-md">
                        <img 
                            :src="getProfileAvatar()" 
                            alt="Avatar Profil" 
                            class="w-full h-full object-cover rounded-full">
                        <!-- Green Pencil Edit Badge (Changes Profile Photo) -->
                        <button 
                            @click="openChangeAvatar()"
                            class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-[#00D06C] border-2 border-[#1657FF] flex items-center justify-center text-white cursor-pointer shadow-2xs hover:scale-105 active:scale-95 transition"
                            :title="t('edit_profile_photo_title', 'Ubah Foto Profil')">
                            <svg class="w-3 h-3 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                            </svg>
                        </button>
                        <!-- Hidden File Input for Avatar Upload -->
                        <input type="file" x-ref="avatarFileInput" @change="handleAvatarUpload($event)" accept="image/*" class="hidden">
                    </div>

                    <!-- User Name & Email -->
                    <h2 class="font-extrabold text-base text-white mt-2.5 tracking-tight" x-text="currentUser?.name || 'Budi Santoso'">Budi Santoso</h2>
                    <p class="text-xs text-white/80 font-medium mt-0.5" x-text="currentUser?.email || 'budi.santoso@email.com'">budi.santoso@email.com</p>
                </div>

                <!-- White Card Container with Rounded Top Corners (No cut-off bottom shadow, fills down smoothly) -->
                <div class="bg-white rounded-t-[32px] -mt-5 pt-6 pb-32 px-5 space-y-3.5 shadow-none min-h-[calc(100vh-220px)] -mx-px w-[calc(100%+2px)]">
                    
                    <!-- FIELD 1: NAMA LENGKAP -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1" x-text="t('full_name_label', 'NAMA LENGKAP')">NAMA LENGKAP</label>
                        <!-- View Mode -->
                        <template x-if="inlineEditingField !== 'name'">
                            <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                                <span class="text-xs font-bold text-zinc-900 flex-1 truncate" x-text="currentUser?.name || 'Budi Santoso'"></span>
                                <button @click="startInlineEdit('name', currentUser?.name || 'Budi Santoso')" class="text-zinc-400 hover:text-zinc-700 transition p-1 cursor-pointer" :title="t('edit_name_title', 'Ubah Nama')">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <!-- Inline Edit Mode -->
                        <template x-if="inlineEditingField === 'name'">
                            <div class="bg-white border-2 border-[#1657FF] rounded-2xl px-3 py-1.5 flex items-center gap-2 shadow-xs">
                                <input 
                                    x-ref="inlineInput_name"
                                    type="text" 
                                    x-model="inlineEditValue" 
                                    @keydown.enter.prevent="saveInlineEdit('name')"
                                    @keydown.escape.prevent="cancelInlineEdit()"
                                    class="text-xs font-bold text-zinc-900 flex-1 focus:outline-none bg-transparent py-1 px-1"
                                    :placeholder="t('full_name_placeholder', 'Masukkan nama lengkap')">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="saveInlineEdit('name')" 
                                        :disabled="inlineEditLoading"
                                        class="w-7 h-7 rounded-xl bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                        :title="t('save_btn', 'Simpan')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="cancelInlineEdit()" 
                                        class="w-7 h-7 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center transition cursor-pointer" 
                                        :title="t('cancel_btn', 'Batal')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- FIELD 2: ALAMAT EMAIL -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1" x-text="t('email_address_label', 'ALAMAT EMAIL')">ALAMAT EMAIL</label>
                        <!-- View Mode -->
                        <template x-if="inlineEditingField !== 'email'">
                            <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                                <span class="text-xs font-medium text-zinc-800 flex-1 truncate" x-text="currentUser?.email || 'budi@example.com'"></span>
                                <button @click="startInlineEdit('email', currentUser?.email || 'budi@example.com')" class="text-zinc-400 hover:text-zinc-700 transition p-1 cursor-pointer" :title="t('edit_email_title', 'Ubah Email')">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <!-- Inline Edit Mode -->
                        <template x-if="inlineEditingField === 'email'">
                            <div class="bg-white border-2 border-[#1657FF] rounded-2xl px-3 py-1.5 flex items-center gap-2 shadow-xs">
                                <input 
                                    x-ref="inlineInput_email"
                                    type="email" 
                                    x-model="inlineEditValue" 
                                    @keydown.enter.prevent="saveInlineEdit('email')"
                                    @keydown.escape.prevent="cancelInlineEdit()"
                                    class="text-xs font-medium text-zinc-800 flex-1 focus:outline-none bg-transparent py-1 px-1"
                                    :placeholder="t('email_placeholder', 'nama@email.com')">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="saveInlineEdit('email')" 
                                        :disabled="inlineEditLoading"
                                        class="w-7 h-7 rounded-xl bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                        :title="t('save_btn', 'Simpan')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="cancelInlineEdit()" 
                                        class="w-7 h-7 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center transition cursor-pointer" 
                                        :title="t('cancel_btn', 'Batal')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- FIELD 3: NO. WHATSAPP AKTIF -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1" x-text="t('active_whatsapp_label', 'NO. WHATSAPP AKTIF')">NO. WHATSAPP AKTIF</label>
                        <!-- View Mode -->
                        <template x-if="inlineEditingField !== 'phone'">
                            <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                                <span class="text-xs font-medium text-zinc-800 flex-1 truncate font-mono" x-text="currentUser?.phone ? ('+' + currentUser.phone.replace(/^\+/, '')) : '+62 812-3456-7890'"></span>
                                <button @click="startInlineEdit('phone', currentUser?.phone || '081234567890')" class="text-zinc-400 hover:text-zinc-700 transition p-1 cursor-pointer" :title="t('edit_phone_title', 'Ubah Nomor WhatsApp')">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <!-- Inline Edit Mode -->
                        <template x-if="inlineEditingField === 'phone'">
                            <div class="bg-white border-2 border-[#1657FF] rounded-2xl px-3 py-1.5 flex items-center gap-2 shadow-xs">
                                <input 
                                    x-ref="inlineInput_phone"
                                    type="tel" 
                                    x-model="inlineEditValue" 
                                    @keydown.enter.prevent="saveInlineEdit('phone')"
                                    @keydown.escape.prevent="cancelInlineEdit()"
                                    class="text-xs font-medium text-zinc-800 flex-1 focus:outline-none bg-transparent font-mono py-1 px-1"
                                    :placeholder="t('phone_placeholder', '081234567890')">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="saveInlineEdit('phone')" 
                                        :disabled="inlineEditLoading"
                                        class="w-7 h-7 rounded-xl bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                        :title="t('save_btn', 'Simpan')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="cancelInlineEdit()" 
                                        class="w-7 h-7 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center transition cursor-pointer" 
                                        :title="t('cancel_btn', 'Batal')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- FIELD 4: NO. KTP / ID -->
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 tracking-wider uppercase block mb-1" x-text="t('identity_card_label', 'NO. KTP / ID')">NO. KTP / ID</label>
                        <!-- View Mode -->
                        <template x-if="inlineEditingField !== 'identity_number'">
                            <div class="bg-white border border-zinc-200/90 rounded-2xl px-4 py-3 flex items-center justify-between shadow-2xs">
                                <span class="text-xs font-medium text-zinc-800 flex-1 truncate font-mono" x-text="profileKtp || currentUser?.identity_number || '3171012505870003'"></span>
                                <button @click="startInlineEdit('identity_number', profileKtp || currentUser?.identity_number || '3171012505870003')" class="text-zinc-400 hover:text-zinc-700 transition p-1 cursor-pointer" :title="t('edit_ktp_title', 'Ubah Nomor KTP')">
                                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <!-- Inline Edit Mode -->
                        <template x-if="inlineEditingField === 'identity_number'">
                            <div class="bg-white border-2 border-[#1657FF] rounded-2xl px-3 py-1.5 flex items-center gap-2 shadow-xs">
                                <input 
                                    x-ref="inlineInput_identity_number"
                                    type="text" 
                                    x-model="inlineEditValue" 
                                    @keydown.enter.prevent="saveInlineEdit('identity_number')"
                                    @keydown.escape.prevent="cancelInlineEdit()"
                                    class="text-xs font-medium text-zinc-800 flex-1 focus:outline-none bg-transparent font-mono py-1 px-1"
                                    :placeholder="t('identity_placeholder', '16 digit NIK/KTP')">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        @click="saveInlineEdit('identity_number')" 
                                        :disabled="inlineEditLoading"
                                        class="w-7 h-7 rounded-xl bg-[#00D06C] hover:bg-[#00B85F] text-white flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                        :title="t('save_btn', 'Simpan')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="cancelInlineEdit()" 
                                        class="w-7 h-7 rounded-xl bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center transition cursor-pointer" 
                                        :title="t('cancel_btn', 'Batal')">
                                        <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- ACTION BUTTON 1: GANTI PASSWORD -->
                    <div class="pt-1">
                        <button 
                            @click="openChangePasswordModal()" 
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
    <div x-show="profileTab === 'alamat'" class="bg-zinc-50/50 min-h-screen px-4 py-4 space-y-3 pb-32 -mx-px w-[calc(100%+2px)]">
        
        <!-- Search Input Bar (Rounded Pill with Magnifying Glass) -->
        <div class="bg-white border border-zinc-200 rounded-full px-4 py-2.5 flex items-center gap-2.5 shadow-2xs">
            <svg class="w-4 h-4 text-zinc-400 stroke-current fill-none shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
                type="text" 
                x-model="addressSearchQuery" 
                :placeholder="t('search_address_placeholder', 'Cari alamat...')" 
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
                            x-text="addr.address?.toLowerCase().includes('thamrin') || addr.address?.toLowerCase().includes('kantor') ? t('office', 'Kantor') : t('home', 'Rumah')">
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
                            :title="t('edit_address_btn', 'Ubah Alamat')">
                            <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                            </svg>
                        </button>
                        <!-- Delete Button (Soft Red Rounded Square with X SVG, for non-default) -->
                        <template x-if="!addr.is_default">
                            <button 
                                @click="deleteAddress(addr.id)" 
                                class="w-7 h-7 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-500 flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                :title="t('delete_address_btn', 'Hapus Alamat')">
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

    <!-- BOTTOM SHEET: GANTI PASSWORD (SWIPEABLE MODAL WITH GRAB HANDLE) -->
    <div 
        x-show="changePasswordModal" 
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>
        
        <!-- Backdrop click to close -->
        <div class="absolute inset-0" @click="closeChangePasswordModal()"></div>

        <!-- Bottom Sheet Container with Drag Handle -->
        <div 
            @click.stop
            x-show="changePasswordModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            :style="sheetDraggingModal === 'changePasswordModal' ? 'transform: translateY(' + Math.max(0, sheetDragOffset) + 'px)' : ''"
            class="relative w-full max-w-[430px] bg-white rounded-t-3xl shadow-2xl p-5 space-y-4 pb-8 max-h-[85vh] overflow-y-auto no-scrollbar z-10 border-t border-zinc-200">
            
            <!-- Swipeable grab handle per user requirement -->
            <div class="pt-1 pb-2 flex justify-center cursor-grab active:cursor-grabbing touch-none select-none"
                 @touchstart="startSheetDrag($event, 'changePasswordModal')"
                 @mousedown="startSheetDrag($event, 'changePasswordModal')">
                <div class="w-12 h-1.5 bg-zinc-300 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-zinc-900" x-text="t('change_password', 'Ganti Password')">Ganti Password</h3>
                        <p class="text-[11px] text-zinc-500" x-text="t('update_password_subtitle', 'Perbarui kata sandi akun Anda')">Perbarui kata sandi akun Anda</p>
                    </div>
                </div>
                <button @click="closeChangePasswordModal()" class="text-zinc-400 hover:text-zinc-600 p-1.5 rounded-full hover:bg-zinc-100 transition cursor-pointer">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <form @submit.prevent="submitChangePassword()" class="space-y-3.5">
                <!-- Password Lama -->
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1" x-text="t('old_password_label', 'Password Lama')">Password Lama</label>
                    <div class="relative">
                        <input 
                            :type="showCurrentPassword ? 'text' : 'password'" 
                            x-model="currentPassword" 
                            :placeholder="t('old_password_placeholder', 'Masukkan password lama')"
                            class="w-full bg-zinc-50 border border-zinc-200/90 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-[#1657FF] focus:bg-white transition pr-10">
                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-600 p-0.5 cursor-pointer">
                            <svg x-show="!showCurrentPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg x-show="showCurrentPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Password Baru (min 8 karakter) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block" x-text="t('new_password_label', 'Password Baru')">Password Baru</label>
                        <span class="text-[10px] text-blue-600 font-medium" x-text="t('min_8_chars', 'Min. 8 karakter')">Min. 8 karakter</span>
                    </div>
                    <div class="relative">
                        <input 
                            :type="showNewPassword ? 'text' : 'password'" 
                            x-model="newPassword" 
                            minlength="8"
                            required
                            :placeholder="t('new_password_placeholder', 'Minimal 8 karakter')"
                            class="w-full bg-zinc-50 border border-zinc-200/90 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-[#1657FF] focus:bg-white transition pr-10">
                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-600 p-0.5 cursor-pointer">
                            <svg x-show="!showNewPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg x-show="showNewPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider block mb-1" x-text="t('confirm_new_password_label', 'Konfirmasi Password Baru')">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            x-model="confirmPassword" 
                            minlength="8"
                            required
                            :placeholder="t('confirm_new_password_placeholder', 'Ketik ulang password baru')"
                            class="w-full bg-zinc-50 border border-zinc-200/90 rounded-xl px-3.5 py-2.5 text-xs text-zinc-900 placeholder-zinc-400 focus:outline-none focus:border-[#1657FF] focus:bg-white transition pr-10">
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-600 p-0.5 cursor-pointer">
                            <svg x-show="!showConfirmPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg x-show="showConfirmPassword" class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-2 flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="closeChangePasswordModal()" 
                        class="w-1/3 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold text-xs rounded-xl transition cursor-pointer"
                        x-text="t('cancel_btn', 'Batal')">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="changePasswordLoading"
                        class="w-2/3 py-2.5 bg-[#1657FF] hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-2">
                        <span x-show="!changePasswordLoading" x-text="t('save_new_password_btn', 'Simpan Password Baru')">Simpan Password Baru</span>
                        <span x-show="changePasswordLoading" x-text="t('saving', 'Menyimpan...')">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
