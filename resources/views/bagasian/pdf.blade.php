<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bagasian Shipment {{ $shipment->shipment_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 12px; line-height: 1.4; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .meta-box { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .order-box { border: 1px solid #cbd5e1; margin-bottom: 15px; border-radius: 4px; padding: 8px; }
        .order-title { font-weight: bold; font-size: 13px; color: #0f172a; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px; text-align: left; }
        th { background: #f1f5f9; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">DOKUMEN PENGIRIMAN BAGASIAN</div>
        <div>No. Shipment: <strong>{{ $shipment->shipment_number }}</strong></div>
        <div>Rute: {{ $shipment->origin_country }} &rarr; {{ $shipment->destination_country }}</div>
        <div>Ref Bagasian: {{ $shipment->bagasian_reference ?? '-' }}</div>
        <div>Tanggal Cetak: {{ now()->format('d M Y H:i') }}</div>
    </div>

    <div class="meta-box">
        <strong>Estimasi Packing & Logistik:</strong><br>
        Estimasi Berat: {{ $shipment->packing_estimate_weight ?? '-' }} | 
        Estimasi Volume: {{ $shipment->packing_estimate_volume ?? '-' }} | 
        Estimasi Biaya: Rp {{ number_format($shipment->packing_estimate_cost ?? 0, 0, ',', '.') }}<br>
        Catatan: {{ $shipment->notes ?? '-' }}
    </div>

    <h4>Daftar Order (Total: {{ $shipment->orders->count() }} Order)</h4>

    @foreach($shipment->orders as $order)
    <div class="order-box">
        <div class="order-title">
            Order #{{ $order->order_number }} | Penerima: {{ $order->address_snapshot['recipient_name'] ?? '-' }} ({{ $order->address_snapshot['phone'] ?? '-' }})
        </div>
        <div>
            Alamat: {{ $order->address_snapshot['address'] ?? '-' }}, {{ $order->address_snapshot['city'] ?? '' }}
            @if(!empty($order->address_snapshot['delivery_note']))
                | Catatan Pengiriman: <em>{{ $order->address_snapshot['delivery_note'] }}</em>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Item</th>
                    <th>Brand</th>
                    <th class="text-right">Qty</th>
                    <th>Tipe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name_snapshot }}</td>
                    <td>{{ $item->brand_name_snapshot ?? '-' }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td>{{ strtoupper($item->availability_type->value) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
