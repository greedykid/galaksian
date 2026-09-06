<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShipmentRequest;
use App\Http\Requests\Admin\UpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(): JsonResponse
    {
        $shipments = Shipment::with(['trip', 'orders'])->latest()->get();

        return $this->successResponse(
            ShipmentResource::collection($shipments),
            'Daftar pengiriman berhasil diambil.'
        );
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['shipment_number'])) {
            $data['shipment_number'] = 'SHP-'.date('Ymd').'-'.strtoupper(Str::random(6));
        }

        $shipment = Shipment::create($data);

        return $this->successResponse(
            new ShipmentResource($shipment),
            'Pengiriman baru berhasil dibuat.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $shipment = Shipment::with(['trip', 'orders.items', 'orders.user'])->findOrFail($id);

        return $this->successResponse(
            new ShipmentResource($shipment),
            'Detail pengiriman berhasil diambil.'
        );
    }

    public function update(int $id, UpdateShipmentRequest $request): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update($request->validated());

        return $this->successResponse(
            new ShipmentResource($shipment->fresh()),
            'Pengiriman berhasil diperbarui.'
        );
    }

    public function sendBagasian(int $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);
        $result = $this->shipmentService->sendToBagasian($shipment);

        return $this->successResponse($result, 'Dokumen Bagasian berhasil digenerate dan siap dikirim.');
    }
}
