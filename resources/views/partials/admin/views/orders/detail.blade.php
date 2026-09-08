<template x-if="activeView === 'order-detail' && selectedOrder">
    <div class="px-4 py-4 space-y-4">
        <!-- Back -->
        <button @click="goTo('orders')" class="flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span x-text="t('back_to_orders', 'Kembali ke Order')"></span>
        </button>

        <!-- Order Header -->
        <div class="bg-zinc-50 rounded-2xl p-4 space-y-2">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-extrabold text-zinc-900" x-text="'#' + selectedOrder.order_number"></p>
                    <p class="text-[11px] text-zinc-500 mt-0.5" x-text="formatDate(selectedOrder.created_at)"></p>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" :class="getStatusColor(selectedOrder.status)" x-text="getStatusLabel(selectedOrder.status)"></span>
            </div>
            <!-- Customer Info (address_snapshot) -->
            <div class="border-t border-zinc-200 pt-2 space-y-1">
                <p class="text-xs font-semibold text-zinc-700" x-text="selectedOrder.address?.recipient_name || '-'"></p>
                <p class="text-[11px] text-zinc-500" x-text="selectedOrder.address?.phone || '-'"></p>
                <p class="text-[11px] text-zinc-500" x-text="selectedOrder.address?.address || '-'"></p>
            </div>
        </div>

        <!-- Items -->
        <div class="space-y-1">
            <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider" x-text="t('order_items', 'Daftar Produk')"></h3>
            <div class="bg-white border border-zinc-200 rounded-xl divide-y divide-zinc-100">
                <template x-for="item in selectedOrder.items" :key="item.id">
                    <div class="p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0 text-lg">📦</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-zinc-900 truncate" x-text="item.product_name || item.name || '-'"></p>
                            <p class="text-[11px] text-zinc-500" x-text="'x' + (item.qty || 1) + ' · ' + formatRupiah(item.price || item.unit_price || 0)"></p>
                        </div>
                        <p class="text-xs font-bold text-zinc-900 tabular shrink-0" x-text="formatRupiah(item.subtotal || ((item.price || item.unit_price || 0) * (item.qty || 1)))"></p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-zinc-50 rounded-xl p-3.5 space-y-1.5">
            <div class="flex justify-between text-xs">
                <span class="text-zinc-500" x-text="t('subtotal', 'Subtotal')"></span>
                <span class="font-semibold tabular" x-text="formatRupiah(selectedOrder.pricing?.product_subtotal ?? selectedOrder.pricing?.raw_subtotal ?? selectedOrder.pricing?.subtotal ?? 0)"></span>
            </div>
            <template x-if="selectedOrder.pricing?.product_discount_amount > 0 || selectedOrder.pricing?.new_user_discount_amount > 0 || selectedOrder.pricing?.voucher_amount > 0">
                <div class="flex justify-between text-xs">
                    <span class="text-zinc-500" x-text="t('discount', 'Diskon')"></span>
                    <span class="font-semibold text-green-600 tabular" x-text="'-' + formatRupiah((selectedOrder.pricing?.product_discount_amount || 0) + (selectedOrder.pricing?.new_user_discount_amount || 0) + (selectedOrder.pricing?.voucher_amount || 0))"></span>
                </div>
            </template>
            <template x-if="selectedOrder.pricing?.handling_fee_amount > 0">
                <div class="flex justify-between text-xs">
                    <span class="text-zinc-500" x-text="t('handling_fee', 'Handling Fee')"></span>
                    <span class="font-semibold tabular" x-text="formatRupiah(selectedOrder.pricing.handling_fee_amount)"></span>
                </div>
            </template>
            <template x-if="selectedOrder.pricing?.shipping_total > 0">
                <div class="flex justify-between text-xs">
                    <span class="text-zinc-500" x-text="t('shipping', 'Ongkir')"></span>
                    <span class="font-semibold tabular" x-text="formatRupiah(selectedOrder.pricing.shipping_total)"></span>
                </div>
            </template>
            <div class="flex justify-between text-sm font-extrabold border-t border-zinc-200 pt-2 mt-1">
                <span x-text="t('grand_total', 'Total')"></span>
                <span class="tabular" x-text="formatRupiah(selectedOrder.pricing?.grand_total ?? selectedOrder.pricing?.product_total ?? 0)"></span>
            </div>
        </div>

        <!-- Invoices -->
        <template x-if="selectedOrder.invoices && selectedOrder.invoices.length > 0">
            <div class="space-y-1">
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider" x-text="t('invoices', 'Invoice')"></h3>
                <div class="space-y-2">
                    <template x-for="inv in selectedOrder.invoices" :key="inv.id">
                        <div class="bg-white border border-zinc-200 rounded-xl p-3 space-y-1">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-zinc-900" x-text="'#' + inv.invoice_number"></p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                      :class="inv.status === 'paid' ? 'bg-green-100 text-green-700' : inv.status === 'expired' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700'"
                                      x-text="inv.status === 'paid' ? (t('paid','Lunas')) : inv.status === 'expired' ? 'Expired' : (t('pending','Pending'))"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-[11px] text-zinc-500" x-text="inv.type === 'product' ? t('product_invoice','Invoice Produk') : inv.type === 'shipping' ? t('shipping_invoice','Invoice Ongkir') : t('additional_invoice','Invoice Tambahan')"></p>
                                <p class="text-xs font-bold tabular" x-text="formatRupiah(inv.amount)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Status History -->
        <template x-if="selectedOrder.status_histories && selectedOrder.status_histories.length > 0">
            <div class="space-y-1">
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider" x-text="t('status_history', 'Riwayat Status')"></h3>
                <div class="bg-white border border-zinc-200 rounded-xl p-3 space-y-2">
                    <template x-for="h in selectedOrder.status_histories" :key="h.id">
                        <div class="flex gap-2.5">
                            <div class="w-2 h-2 rounded-full bg-zinc-300 mt-1.5 shrink-0"></div>
                            <div>
                                <p class="text-[11px] font-semibold text-zinc-700" x-text="getStatusLabel(h.to_status)"></p>
                                <p class="text-[10px] text-zinc-400" x-text="formatDate(h.created_at)"></p>
                                <p class="text-[10px] text-zinc-500" x-show="h.note" x-text="h.note"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- ADMIN ACTIONS -->
        <div class="space-y-3 bg-zinc-50 rounded-2xl p-4">
            <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider" x-text="t('admin_actions', 'Aksi Admin')"></h3>

            <!-- Update Status -->
            <div class="space-y-1.5">
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('change_status', 'Ubah Status')"></label>
                <select x-model="actionStatus" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition">
                    <template x-for="st in orderStatusList" :key="st.value">
                        <option :value="st.value" x-text="st.label" :selected="st.value === selectedOrder.status"></option>
                    </template>
                </select>
            </div>

            <!-- Shipping Costs (show when transitioning to ready_for_delivery) -->
            <template x-if="actionStatus === 'ready_for_delivery' || actionStatus === 'pending_payment_shipping'">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('shipping_jastip', 'Ongkir Jastip (Rp)')"></label>
                    <input type="number" x-model.number="actionShippingJastip" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition tabular" placeholder="0">
                    <label class="text-[11px] font-semibold text-zinc-600" x-text="t('shipping_local', 'Ongkir Lokal (Rp)')"></label>
                    <input type="number" x-model.number="actionShippingLocal" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition tabular" placeholder="0">
                </div>
            </template>

            <!-- Note -->
            <div class="space-y-1">
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('note', 'Catatan')"></label>
                <input type="text" x-model="actionNote" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"
                       :placeholder="t('optional_note', 'Catatan opsional...')">
            </div>

            <button @click="doUpdateStatus()" :disabled="actionLoading"
                    class="w-full bg-zinc-900 text-white font-bold text-xs py-3 rounded-xl hover:bg-zinc-800 active:scale-[0.98] transition disabled:opacity-50">
                <span x-show="!actionLoading" x-text="t('update_status', 'Update Status')"></span>
                <span x-show="actionLoading" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
            </button>

            <!-- Assign Shipment -->
            <div class="border-t border-zinc-200 pt-3 space-y-1.5">
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('assign_shipment', 'Assign ke Shipment')"></label>
                <div class="flex gap-2">
                    <input type="number" x-model.number="actionShipmentId" class="flex-1 bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition tabular" placeholder="Shipment ID">
                    <button @click="doAssignShipment()" :disabled="!actionShipmentId || actionLoading"
                            class="px-4 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition disabled:opacity-50" x-text="t('assign', 'Assign')"></button>
                </div>
            </div>

            <!-- Create Additional Invoice -->
            <div class="border-t border-zinc-200 pt-3 space-y-1.5">
                <label class="text-[11px] font-semibold text-zinc-600" x-text="t('create_invoice', 'Buat Invoice Tambahan')"></label>
                <input type="number" x-model.number="actionInvoiceAmount" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition tabular" placeholder="Nominal (Rp)">
                <input type="text" x-model="actionInvoiceDesc" class="w-full bg-white border border-zinc-300 rounded-xl px-3 py-2.5 text-xs font-medium placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-red/30 transition"
                       :placeholder="t('invoice_description', 'Deskripsi invoice...')">
                <button @click="doCreateInvoice()" :disabled="!actionInvoiceAmount || actionLoading"
                        class="w-full bg-amber-600 text-white font-bold text-xs py-3 rounded-xl hover:bg-amber-700 transition disabled:opacity-50"
                        x-text="t('create_additional_invoice', 'Terbitkan Invoice')"></button>
            </div>
        </div>

        <!-- Refunds -->
        <template x-if="selectedOrder.refunds && selectedOrder.refunds.length > 0">
            <div class="space-y-1">
                <h3 class="text-xs font-extrabold text-zinc-900 uppercase tracking-wider" x-text="t('refunds', 'Refund')"></h3>
                <div class="space-y-2">
                    <template x-for="ref in selectedOrder.refunds" :key="ref.id">
                        <div class="bg-white border border-zinc-200 rounded-xl p-3 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-semibold text-zinc-900" x-text="formatRupiah(ref.amount)"></p>
                                <p class="text-[11px] text-zinc-500" x-text="ref.reason || '-'"></p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                  :class="['approved','completed'].includes(ref.status) ? 'bg-green-100 text-green-700' : ref.status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700'"
                                  x-text="ref.status"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
