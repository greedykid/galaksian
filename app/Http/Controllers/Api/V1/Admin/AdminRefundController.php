<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessRefundRequest;
use App\Http\Resources\RefundResource;
use App\Models\Order;
use App\Models\Refund;
use App\Services\AdminActivityLogService;
use App\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;

class AdminRefundController extends Controller
{
    public function __construct(
        protected RefundService $refundService,
        protected AdminActivityLogService $activityLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $refunds = Refund::with(['order', 'invoice', 'approver'])->latest()->paginate(20);

        return $this->successResponse(
            RefundResource::collection($refunds)->response()->getData(true),
            'Daftar refund berhasil diambil.'
        );
    }

    public function store(ProcessRefundRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->validated('order_id'));
        $refund = $this->refundService->createRefund($order, $request->validated());

        $this->activityLogService->log(
            $request->user(),
            'create_refund',
            "Buat refund #{$refund->id} utk order #{$order->order_number} sebesar Rp ".number_format($refund->amount, 0, ',', '.'),
            $refund,
            ['order_id' => $order->id, 'amount' => $refund->amount],
            RequestFacade::ip()
        );

        return $this->successResponse(
            new RefundResource($refund),
            'Pengajuan refund berhasil dibuat.',
            201
        );
    }

    public function approve(int $id, Request $request): JsonResponse
    {
        $refund = Refund::findOrFail($id);
        $refund = $this->refundService->approveRefund($refund, $request->user());

        $this->activityLogService->log(
            $request->user(),
            'approve_refund',
            "Setujui refund #{$refund->id} sebesar Rp ".number_format($refund->amount, 0, ',', '.'),
            $refund,
            ['amount' => $refund->amount, 'status' => $refund->status?->value],
            RequestFacade::ip()
        );

        return $this->successResponse(
            new RefundResource($refund),
            'Refund berhasil disetujui.'
        );
    }

    public function reject(int $id, Request $request): JsonResponse
    {
        $refund = Refund::findOrFail($id);
        $reason = $request->input('reason', 'Ditolak oleh admin.');
        $refund = $this->refundService->rejectRefund($refund, $request->user(), $reason);

        $this->activityLogService->log(
            $request->user(),
            'reject_refund',
            "Tolak refund #{$refund->id} sebesar Rp ".number_format($refund->amount, 0, ',', '.')." ({$reason})",
            $refund,
            ['amount' => $refund->amount, 'reason' => $reason],
            RequestFacade::ip()
        );

        return $this->successResponse(
            new RefundResource($refund),
            'Refund berhasil ditolak.'
        );
    }
}
