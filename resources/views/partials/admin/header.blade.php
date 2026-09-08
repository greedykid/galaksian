<header class="sticky top-0 z-50 bg-zinc-900 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-lg bg-brand-red flex items-center justify-center">
            <span class="text-white text-xs font-extrabold">G</span>
        </div>
        <span class="text-white text-sm font-bold tracking-tight" x-text="t('admin_title', 'Admin Panel')"></span>
    </div>
    <div class="flex items-center gap-2">
        <button @click="toggleLang()" class="text-zinc-400 text-[11px] font-bold uppercase bg-zinc-800 px-2 py-1 rounded-md" x-text="currentLang === 'id' ? 'EN' : 'ID'"></button>
        <button @click="logout()" class="text-zinc-400 hover:text-white transition p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
    </div>
</header>
