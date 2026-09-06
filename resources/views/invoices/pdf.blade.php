<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 13px; line-height: 1.5; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 15px; }
        .title { font-size: 20px; font-weight: bold; color: #1e3a8a; }
        .meta-table, .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9fafb; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">GALAKSIAN JASTIP</div>
        <div>Invoice #{{ $invoice->invoice_number }}</div>
        <div>Tanggal: {{ $invoice->created_at->format('d M Y H:i') }}</div>
        <div>Status: <strong>{{ strtoupper($invoice->status->value) }}</strong></div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Detail Penerima:</strong><br>
                Nama: {{ $invoice->order->address_snapshot['recipient_name'] ?? '-' }}<br>
                Telepon: {{ $invoice->order->address_snapshot['phone'] ?? '-' }}<br>
                Alamat: {{ $invoice->order->address_snapshot['address'] ?? '-' }}
            </td>
            <td style="width: 50%; vertical-align: top;">
                <strong>Detail Order:</strong><br>
                No Order: {{ $invoice->order->order_number }}<br>
                Tipe Tagihan: {{ strtoupper($invoice->type->value) }}<br>
                Metode Pembayaran: {{ $invoice->payment_method?->value ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
            <tr>
                <td>
                    {{ $item->product_name_snapshot }}<br>
                    <small style="color: #6b7280;">Brand: {{ $item->brand_name_snapshot ?? '-' }} | SKU: {{ $item->sku_snapshot ?? '-' }}</small>
                </td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($invoice->type->value === 'product')
            <tr>
                <td colspan="3" class="text-right">Subtotal Produk:</td>
                <td class="text-right">Rp {{ number_format($invoice->order->product_subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->order->product_discount_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Diskon Promo:</td>
                <td class="text-right">- Rp {{ number_format($invoice->order->product_discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->order->new_user_discount_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Diskon User Baru:</td>
                <td class="text-right">- Rp {{ number_format($invoice->order->new_user_discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->order->voucher_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Voucher:</td>
                <td class="text-right">- Rp {{ number_format($invoice->order->voucher_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->order->handling_fee_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Biaya Penanganan:</td>
                <td class="text-right">Rp {{ number_format($invoice->order->handling_fee_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endif

            <tr class="total-row">
                <td colspan="3" class="text-right">Total Tagihan:</td>
                <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
