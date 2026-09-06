<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResolveOosItemRequest;
use App\Http\Requests\User\StoreReviewRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ReviewResource;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Review;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService,
        protected PdfService $pdfService,
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type', 'berlangsung');

        $query = Order::where('user_id', $user->id);

        if ($type === 'selesai' || $type === 'completed') {
            $query->where('status', OrderStatus::COMPLETED);
        } elseif ($type === 'dibatalkan' || $type === 'cancelled') {
            $query->whereIn('status', [
                OrderStatus::CANCELLED,
                OrderStatus::REFUNDED,
            ]);
        } else {
            // Default: berlangsung / pending
            $query->whereIn('status', [
                OrderStatus::PENDING_PAYMENT_PRODUCT,
                OrderStatus::PAID_PRODUCT,
                OrderStatus::PROCESSING,
                OrderStatus::PACKING,
                OrderStatus::READY_FOR_DELIVERY,
                OrderStatus::PENDING_PAYMENT_SHIPPING,
                OrderStatus::SHIPPING_PAID,
                OrderStatus::DELIVERING,
                OrderStatus::REFUND_REQUESTED,
            ]);
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('items', function ($iq) use ($search) {
                        $iq->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->with(['items', 'invoices'])->orderByDesc('id')->paginate(15);

        return $this->successResponse(
            OrderResource::collection($orders)->response()->getData(true),
            'Daftar pesanan berhasil diambil.'
        );
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $order = Order::with(['items', 'invoices.payments', 'statusHistories', 'trip', 'shipment'])->findOrFail($id);

        Gate::authorize('view', $order);

        return $this->successResponse(
            new OrderDetailResource($order),
            'Detail pesanan berhasil diambil.'
        );
    }

    public function payInvoice(int $orderId, int $invoiceId, Request $request): JsonResponse
    {
        $order = Order::findOrFail($orderId);
        Gate::authorize('view', $order);

        $invoice = Invoice::where('order_id', $order->id)->where('id', $invoiceId)->firstOrFail();

        if ($invoice->status === InvoiceStatus::PAID) {
            throw new BusinessException('Invoice ini sudah dibayar.');
        }

        $paymentMethod = $request->input('payment_method', $invoice->payment_method?->value ?? 'qris');
        $charge = $this->paymentGatewayService->createCharge($invoice, $paymentMethod);

        return $this->successResponse($charge, 'Instruksi pembayaran berhasil dibuat.');
    }

    public function downloadInvoice(int $orderId, int $invoiceId, Request $request): BinaryFileResponse
    {
        $order = Order::findOrFail($orderId);
        Gate::authorize('view', $order);

        $invoice = Invoice::where('order_id', $order->id)->where('id', $invoiceId)->firstOrFail();

        $pdfPath = "private/invoices/{$invoice->invoice_number}.pdf";
        if (! Storage::disk('local')->exists($pdfPath)) {
            $this->pdfService->generateInvoicePdf($invoice);
        }

        $fullPath = Storage::disk('local')->path($pdfPath);

        return response()->download($fullPath, "Invoice-{$invoice->invoice_number}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function review(int $orderId, StoreReviewRequest $request): JsonResponse
    {
        $order = Order::findOrFail($orderId);
        Gate::authorize('view', $order);

        $productId = $request->validated('product_id');

        // Pastikan order berisi produk tersebut
        $item = $order->items()->where('product_id', $productId)->first();
        if (! $item) {
            throw new BusinessException('Produk tidak ada dalam pesanan ini.');
        }

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'order_id' => $order->id,
                'product_id' => $productId,
            ],
            [
                'rating' => $request->validated('rating'),
                'comment' => $request->validated('comment'),
                'images' => $request->validated('images'),
                'status' => 'published',
            ]
        );

        return $this->successResponse(
            new ReviewResource($review),
            'Ulasan berhasil disimpan.'
        );
    }

    public function resolveOosItem(int $orderId, int $itemId, ResolveOosItemRequest $request): JsonResponse
    {
        $order = Order::findOrFail($orderId);
        Gate::authorize('view', $order);

        $order = $this->orderService->resolveOosItem(
            $order,
            $itemId,
            $request->validated(),
            $request->user()
        );

        return $this->successResponse(
            new OrderDetailResource($order),
            'Penanganan barang habis (OOS) berhasil diproses.'
        );
    }
}
