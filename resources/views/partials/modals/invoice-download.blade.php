<!-- ========================================================= -->
<!-- MODAL: PILIH INVOICE UNTUK DIUNDUH                        -->
<!-- ========================================================= -->
<div 
    x-show="showInvoiceDownloadModal" 
    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-0 sm:p-4" 
    @click.self="showInvoiceDownloadModal = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    x-cloak>
    <div class="w-full max-w-[430px] bg-white rounded-t-2xl sm:rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
        <!-- Modal Header -->
        <div class="px-4 py-3 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/80">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-zinc-950 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-zinc-950 tracking-tight" x-text="t('choose_invoice_modal_title', 'Pilih Invoice untuk Diunduh')">Pilih Invoice untuk Diunduh</h3>
                    <p class="text-[10px] text-zinc-500 font-mono" x-text="selectedInvoiceOrder ? selectedInvoiceOrder.order_number : ''"></p>
                </div>
            </div>
            <button @click="showInvoiceDownloadModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 transition text-sm font-bold min-h-[32px]">
                ✕
            </button>
        </div>

        <!-- Invoices List -->
        <div class="p-4 space-y-3 overflow-y-auto max-h-[60vh]">
            <p class="text-[11px] text-zinc-600 leading-relaxed" x-text="t('choose_invoice_modal_subtitle', 'Pesanan ini memiliki beberapa invoice. Pilih file yang ingin Anda unduh:')">
                Pesanan ini memiliki beberapa invoice. Pilih file yang ingin Anda unduh:
            </p>

            <template x-if="selectedInvoiceOrder && selectedInvoiceOrder.invoices">
                <div class="space-y-2.5">
                    <template x-for="inv in selectedInvoiceOrder.invoices" :key="inv.id">
                        <div class="p-3 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition shadow-2xs space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span 
                                            :class="getInvoiceTypeBadgeClass(inv.type)"
                                            class="text-[9px] font-bold px-1.5 py-0.5 rounded uppercase font-mono tracking-wider" 
                                            x-text="getInvoiceTypeLabel(inv.type)">
                                        </span>
                                        <span 
                                            :class="inv.status === 'paid' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200'"
                                            class="text-[9px] font-bold px-1.5 py-0.5 rounded border" 
                                            x-text="inv.status === 'paid' ? t('status_paid', 'LUNAS') : t('status_unpaid', 'MENUNGGU PEMBAYARAN')">
                                        </span>
                                    </div>
                                    <h4 class="font-mono font-bold text-xs text-zinc-900" x-text="inv.invoice_number"></h4>
                                    <template x-if="inv.description">
                                        <p class="text-[10px] text-zinc-500 leading-tight" x-text="inv.description"></p>
                                    </template>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[10px] text-zinc-400 block" x-text="t('invoice_amount_label', 'Nominal:')">Nominal:</span>
                                    <span class="font-black text-zinc-950 tabular text-xs" x-text="formatRupiah(inv.amount)"></span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-zinc-100 flex items-center justify-between gap-2">
                                <span class="text-[10px] text-zinc-400 font-mono" x-text="formatDate(inv.created_at)"></span>
                                <button 
                                    @click="downloadInvoicePdf(selectedInvoiceOrder.id, inv.id)" 
                                    class="px-3 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 min-h-[36px] shadow-2xs">
                                    <svg class="w-3.5 h-3.5 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span x-text="t('download_this_invoice_btn', 'Unduh PDF Ini')">Unduh PDF Ini</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Modal Footer: Download All Option -->
        <div class="p-4 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between gap-3">
            <button 
                @click="showInvoiceDownloadModal = false" 
                class="flex-1 py-2.5 bg-white border border-zinc-200 text-zinc-700 hover:bg-zinc-100 rounded-xl text-xs font-bold transition min-h-[44px]"
                x-text="t('cancel', 'Batal')">
                Batal
            </button>
            <template x-if="selectedInvoiceOrder && selectedInvoiceOrder.invoices && selectedInvoiceOrder.invoices.length > 1">
                <button 
                    @click="downloadAllInvoices(selectedInvoiceOrder)" 
                    class="flex-1 py-2.5 bg-[#E60012] hover:bg-red-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 min-h-[44px] shadow-xs">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span x-text="t('download_all_invoices_btn', 'Unduh Semua')">Unduh Semua</span>
                </button>
            </template>
        </div>
    </div>
</div>
