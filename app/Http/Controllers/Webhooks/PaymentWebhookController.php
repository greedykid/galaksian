<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $result = $this->paymentGatewayService->handleWebhook(
            $request->all(),
            $request->headers->all()
        );

        return response()->json([
            'success' => true,
            'message' => 'Webhook berhasil diproses.',
            'data' => $result,
        ]);
    }
}
