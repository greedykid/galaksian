<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Exceptions\BusinessException;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function __construct(
        protected PdfService $pdfService,
        protected WhatsappService $whatsappService,
        protected OrderStatusService $orderStatusService
    ) {}

    public function assignOrders(Shipment $shipment, array $orderIds): void
    {
        DB::transaction(function () use ($shipment, $orderIds) {
            $orders = Order::whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                $order->update(['shipment_id' => $shipment->id]);

                if ($order->status === OrderStatus::PAID_PRODUCT) {
                    $this->orderStatusService->transition(
                        $order,
                        OrderStatus::PACKING,
                        "Order ditugaskan ke pengiriman #{$shipment->shipment_number}."
                    );
                }
            }

            if ($shipment->status === ShipmentStatus::DRAFT) {
                $shipment->update(['status' => ShipmentStatus::ASSIGNED]);
            }
        });
    }

    public function updatePackingEstimate(Shipment $shipment, array $data): Shipment
    {
        $shipment->update([
            'packing_estimate_weight' => $data['packing_estimate_weight'] ?? $shipment->packing_estimate_weight,
            'packing_estimate_volume' => $data['packing_estimate_volume'] ?? $shipment->packing_estimate_volume,
            'packing_estimate_cost' => $data['packing_estimate_cost'] ?? $shipment->packing_estimate_cost,
            'notes' => $data['notes'] ?? $shipment->notes,
        ]);

        return $shipment;
    }

    public function sendToBagasian(Shipment $shipment): array
    {
        if ($shipment->orders()->count() === 0) {
            throw new BusinessException('Shipment belum memiliki order untuk dikirim ke Bagasian.');
        }

        $pdfPath = $this->pdfService->generateBagasianPdf($shipment);

        $adminPhone = Setting::get('admin_whatsapp_number', '6281200000001');
        $message = "Halo Bagasian, berikut dokumen pengiriman Galaksian untuk Shipment #{$shipment->shipment_number} dengan total {$shipment->orders()->count()} order.";

        $waLink = $this->whatsappService->createShareLink($adminPhone, $message);

        $shipment->update([
            'status' => ShipmentStatus::SENT_TO_BAGASIAN,
            'sent_to_bagasian_at' => now(),
            'wa_message' => $message,
        ]);

        return [
            'pdf_path' => $pdfPath,
            'wa_link' => $waLink,
            'status' => ShipmentStatus::SENT_TO_BAGASIAN->value,
            'sent_at' => $shipment->sent_to_bagasian_at?->toIso8601String(),
        ];
    }
}
