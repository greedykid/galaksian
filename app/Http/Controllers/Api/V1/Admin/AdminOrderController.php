<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignOrdersRequest;
use App\Http\Requests\Admin\CreateAdditionalInvoiceRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\InvoiceService;
use App\Services\OrderStatusService;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(
        protected OrderStatusService $orderStatusService,
        protected InvoiceService $invoiceService,
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'items', 'invoices']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->whereLike('order_number', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->whereLike('phone', "%{$search}%")->orWhereLike('name', "%{$search}%"));
            });
        }

        $perPage = min((int) $request->query('per_page', 20), 100);
        $orders = $query->latest()->paginate($perPage);

        return $this->successResponse(
            OrderResource::collection($orders)->response()->getData(true),
            'Daftar order admin berhasil diambil.'
        );
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'items', 'invoices.payments', 'statusHistories', 'trip', 'shipment'])->findOrFail($id);

        return $this->successResponse(
            new OrderDetailResource($order),
            'Detail order berhasil diambil.'
        );
    }

    public function updateStatus(int $id, UpdateOrderStatusRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);
        $newStatus = OrderStatus::from($request->validated('status'));
        $note = $request->validated('note');

        // Jika ada input ongkir
        $jastip = $request->validated('shipping_jastip_amount');
        $local = $request->validated('shipping_local_amount');

        if ($jastip !== null || $local !== null) {
            $order->shipping_jastip_amount = $jastip ?? $order->shipping_jastip_amount ?? 0;
            $order->shipping_local_amount = $local ?? $order->shipping_local_amount ?? 0;
            $order->shipping_total = (int) $order->shipping_jastip_amount + (int) $order->shipping_local_amount;
            $order->grand_total = (int) $order->product_total + (int) $order->shipping_total;
            $order->save();
        }

        // Transisi status order melalui OrderStatusService
        $order = $this->orderStatusService->transition($order, $newStatus, $note, $request->user());

        // Jika status siap diantar dan ongkir ada, buat invoice shipping jika belum ada
        if (in_array($newStatus, [OrderStatus::READY_FOR_DELIVERY, OrderStatus::PENDING_PAYMENT_SHIPPING], true)) {
            if ($order->shipping_total > 0 && ! $order->shippingInvoice()->exists()) {
                $this->invoiceService->createShippingInvoice($order);
            }
        }

        return $this->successResponse(
            new OrderDetailResource($order->fresh(['invoices'])),
            'Status order berhasil diperbarui.'
        );
    }

    public function assignShipment(int $id, AssignOrdersRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);
        $shipmentId = $request->validated('shipment_id');
        $shipment = Shipment::findOrFail($shipmentId);

        $this->shipmentService->assignOrders($shipment, [$order->id]);

        return $this->successResponse(
            new OrderDetailResource($order->fresh(['shipment'])),
            'Order berhasil ditugaskan ke shipment.'
        );
    }

    public function createInvoice(int $id, CreateAdditionalInvoiceRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);
        $invoice = $this->invoiceService->createAdditionalInvoice($order, $request->validated(), $request->user());

        return $this->successResponse(
            new InvoiceResource($invoice),
            'Invoice tambahan berhasil dibuat.',
            201
        );
    }
}
