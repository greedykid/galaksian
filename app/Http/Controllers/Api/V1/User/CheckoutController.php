<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\OrderDetailResource;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $result = $this->checkoutService->checkout(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse([
            'order' => new OrderDetailResource($result['order']),
            'invoice' => new InvoiceResource($result['invoice']),
            'payment' => $result['payment'],
        ], 'Checkout pesanan berhasil dilakukan.', 201);
    }
}
