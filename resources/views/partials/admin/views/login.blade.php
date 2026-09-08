<template x-if="!isLoggedIn">
    <div class="min-h-screen flex flex-col items-center justify-center px-6 bg-zinc-900">
        <div class="w-full max-w-sm space-y-6">
            <!-- Logo -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-brand-red mx-auto flex items-center justify-center shadow-lg">
                    <span class="text-white text-2xl font-extrabold">G</span>
                </div>
                <h1 class="text-white text-xl font-extrabold tracking-tight" x-text="t('admin_login_title', 'Galaksian Admin')"></h1>
                <p class="text-zinc-400 text-xs font-medium" x-text="t('admin_login_subtitle', 'Masuk untuk mengelola toko')"></p>
            </div>

            <!-- Login Form -->
            <div class="space-y-3">
                <div>
                    <label class="text-zinc-400 text-xs font-semibold mb-1 block" x-text="t('email_label', 'Email')"></label>
                    <input type="email" x-model="loginEmail" 
                           class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-brand-red/50 focus:border-brand-red transition"
                           placeholder="admin@galaksian.com">
                </div>
                <div>
                    <label class="text-zinc-400 text-xs font-semibold mb-1 block" x-text="t('password_label', 'Password')"></label>
                    <input type="password" x-model="loginPassword" @keydown.enter="doLogin()"
                           class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-brand-red/50 focus:border-brand-red transition"
                           :placeholder="t('password_placeholder', 'Masukkan password')">
                </div>
                <button @click="doLogin()" :disabled="loginLoading"
                        class="w-full bg-brand-red text-white font-bold text-sm py-3.5 rounded-xl shadow-lg hover:bg-red-700 active:scale-[0.98] transition disabled:opacity-50">
                    <span x-show="!loginLoading" x-text="t('login_button', 'Masuk')"></span>
                    <span x-show="loginLoading" class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Loading...
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
