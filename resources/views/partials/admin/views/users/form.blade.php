<template x-if="activeView === 'user-form'">
    <div class="px-4 py-4 space-y-4">
        <button @click="goTo('users')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_users','Kembali ke User')"></span>
        </button>
        <h1 class="text-lg font-extrabold text-zinc-900" x-text="t('edit_user','Edit User')"></h1>
        <div class="space-y-3">
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Nama</label>
                <input type="text" x-model="userForm.name" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Phone</label>
                <input type="text" x-model="userForm.phone" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
            <div>
                <label class="text-[11px] font-semibold text-zinc-600">Email</label>
                <input type="email" x-model="userForm.email" class="w-full bg-zinc-100 border-none rounded-xl px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
            </div>
        </div>
        <button @click="saveUser()" class="w-full bg-zinc-900 text-white font-bold text-sm py-3.5 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition" x-text="t('save','Simpan')"></button>
    </div>
</template>
