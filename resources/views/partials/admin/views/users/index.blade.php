<template x-if="activeView === 'users'">
    <div class="px-4 py-4 space-y-3">
        <button @click="goTo('more')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_menu', 'Kembali ke Menu')"></span>
        </button>
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('user_management', 'Manajemen User')"></h1>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" x-model="userSearch" @input.debounce.400ms="searchUsers()" class="w-full bg-zinc-100 border-none rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition" :placeholder="t('search_user_placeholder','Cari nama, HP, email...')">
        </div>
        <div class="space-y-2">
            <template x-if="usersLoading"><div class="flex justify-center py-8"><svg class="w-6 h-6 animate-spin text-zinc-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg></div></template>
            <template x-for="u in users" :key="u.id">
                <div class="bg-white border border-zinc-200 rounded-xl p-3.5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-zinc-100 flex items-center justify-center shrink-0 font-bold text-zinc-500" x-text="(u.name || 'U').charAt(0).toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-zinc-900 truncate" x-text="u.name || '-'"></p>
                        <p class="text-[11px] text-zinc-500" x-text="u.phone || '-'"></p>
                        <p class="text-[10px] text-zinc-400" x-text="u.email || ''"></p>
                    </div>
                    <button @click="editUser(u); activeView='user-form'" class="w-7 h-7 rounded-lg bg-zinc-100 hover:bg-zinc-200 flex items-center justify-center text-zinc-500 hover:text-blue-600 transition shrink-0" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                </div>
            </template>
            <template x-if="users.length === 0 && !usersLoading"><p class="text-center text-xs text-zinc-400 py-8" x-text="t('no_users','Tidak ada user')"></p></template>
        </div>
        <template x-if="usersMeta.last_page > 1">
            <div class="flex items-center justify-center gap-2 pt-2">
                <button @click="usersPage--; loadUsers()" :disabled="usersPage <= 1" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">←</button>
                <span class="text-xs font-semibold text-zinc-500" x-text="usersPage + ' / ' + usersMeta.last_page"></span>
                <button @click="usersPage++; loadUsers()" :disabled="usersPage >= usersMeta.last_page" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-zinc-100 text-zinc-600 disabled:opacity-30 transition">→</button>
            </div>
        </template>
    </div>
</template>
