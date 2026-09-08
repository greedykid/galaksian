{{-- Voucher list komponen bersama (dipakai di cart & checkout) --}}
{{-- Props: $style = 'card' (kartu penuh) | 'compact' (baris kecil) --}}
@php
    $isCard = ($style ?? 'card') === 'card';
    $cardClass = $isCard
        ? 'rounded-2xl border p-3.5 space-y-2.5 shadow-2xs mb-2.5'
        : 'rounded-xl border p-3 gap-2';
@endphp

<template x-for="v in (voucherList || []).slice(0, showAllVouchers ? (voucherList || []).length : 2)" :key="'voucher-' + v.code">
    <div class="{{ $cardClass }} {{ $isCard ? '' : 'flex items-center justify-between mb-2' }}"
         :class="isVoucherUnavailable(v) ? 'border-zinc-200 bg-zinc-50/60' : (v.type === 'fixed' ? 'border-blue-200 bg-blue-50/20' : 'border-amber-400 bg-amber-50/15')">

        {{-- Left: icon + title/desc --}}
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center"
                 :class="isVoucherUnavailable(v) ? 'bg-zinc-100 text-zinc-400' : (v.type === 'fixed' ? 'bg-blue-100 text-[#1657FF]' : 'bg-pink-100 text-pink-500')">
                <template x-if="v.icon === 'tag'">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                </template>
                <template x-if="v.icon === 'percent'">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="5" x2="5" y2="19"></line>
                        <circle cx="6.5" cy="6.5" r="2.5"></circle>
                        <circle cx="17.5" cy="17.5" r="2.5"></circle>
                    </svg>
                </template>
            </div>
            <div>
                <h4 class="font-bold text-xs text-zinc-900" x-text="v.title"></h4>
                <p class="text-[10px] text-zinc-500" x-text="v.description"></p>
            </div>
        </div>

        {{-- Bottom row (card style): status + button --}}
        @if ($isCard)
            <div class="border-t border-dashed pt-2 flex items-center justify-between"
                 :class="isVoucherUnavailable(v) ? 'border-zinc-200' : (v.type === 'fixed' ? 'border-blue-200' : 'border-amber-300')">
                <div class="flex items-center gap-1.5 text-[10px] font-semibold"
                     :class="isVoucherUnavailable(v) ? 'text-zinc-400' : (getVoucherState(v.code) === 'applied' ? 'text-emerald-700' : (v.type === 'fixed' ? 'text-zinc-400' : 'text-emerald-700'))">
                    <span class="w-1.5 h-1.5 rounded-full"
                          :class="isVoucherUnavailable(v) ? 'bg-zinc-300' : (getVoucherState(v.code) === 'applied' || v.type !== 'fixed' ? 'bg-[#00D06C]' : 'bg-zinc-400')"></span>
                    <span x-text="isVoucherUnavailable(v) ? getVoucherBlockReason(v.state) : (getVoucherState(v.code) === 'applied' ? t('voucher_active_applied', 'Voucher aktif terpasang') : (getVoucherState(v.code) === 'claimed' ? t('ready_to_use_title', 'Siap digunakan') : t('available', 'Tersedia')))"></span>
                </div>
                <button
                    @click="handleVoucherAction(v.code)"
                    :disabled="isVoucherUnavailable(v)"
                    :class="isVoucherUnavailable(v) ? 'bg-zinc-200 text-zinc-400 cursor-not-allowed' : (getVoucherState(v.code) === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState(v.code) === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700'))"
                    class="px-4 py-1.5 font-extrabold text-xs rounded-lg shadow-2xs transition active:scale-95 cursor-pointer">
                    <span x-text="isVoucherUnavailable(v) ? t('used_btn', 'Dipakai') : (getVoucherState(v.code) === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState(v.code) === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim')))"></span>
                </button>
            </div>
        @else
            <button
                @click="handleVoucherAction(v.code)"
                :disabled="isVoucherUnavailable(v)"
                :class="isVoucherUnavailable(v) ? 'bg-zinc-200 text-zinc-400 cursor-not-allowed' : (getVoucherState(v.code) === 'applied' ? 'bg-[#00D06C] text-white hover:bg-emerald-600' : (getVoucherState(v.code) === 'claimed' ? 'bg-amber-400 text-zinc-950 hover:bg-amber-500' : 'bg-[#1657FF] text-white hover:bg-blue-700'))"
                class="shrink-0 px-3.5 py-1.5 font-extrabold text-xs rounded-xl shadow-2xs transition active:scale-95 cursor-pointer">
                <span x-text="isVoucherUnavailable(v) ? t('used_btn', 'Dipakai') : (getVoucherState(v.code) === 'applied' ? t('used_btn', 'Dipakai') : (getVoucherState(v.code) === 'claimed' ? t('use_btn', 'Pakai') : t('claim_btn', 'Klaim')))"></span>
            </button>
        @endif
    </div>
</template>

{{-- Tombol expand/collapse: tampil jika ada lebih dari 2 voucher --}}
<template x-if="(voucherList || []).length > 2">
    <button
        @click="showAllVouchers = !showAllVouchers"
        class="w-full mt-2 py-2 text-[11px] font-bold text-[#1657FF] hover:text-blue-700 bg-blue-50/60 hover:bg-blue-100/60 rounded-xl border border-blue-100 transition active:scale-[0.99] cursor-pointer flex items-center justify-center gap-1">
        <span x-text="showAllVouchers ? t('show_less_vouchers', 'Sembunyikan Voucher') : t('show_all_vouchers', 'Tampilkan Semua Voucher')"></span>
        <svg class="w-3.5 h-3.5 stroke-current fill-none transition-transform" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :class="showAllVouchers ? 'rotate-180' : ''">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>
</template>
