<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTripRequest;
use App\Http\Requests\Admin\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;

class AdminTripController extends Controller
{
    public function index(): JsonResponse
    {
        $trips = Trip::withCount('orders')->latest()->get();

        return $this->successResponse(
            TripResource::collection($trips),
            'Daftar trip berhasil diambil.'
        );
    }

    public function store(StoreTripRequest $request): JsonResponse
    {
        $trip = Trip::create($request->validated());

        return $this->successResponse(
            new TripResource($trip),
            'Trip berhasil dibuat.',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $trip = Trip::with(['orders', 'shipments'])->findOrFail($id);

        return $this->successResponse(
            new TripResource($trip),
            'Detail trip berhasil diambil.'
        );
    }

    public function update(int $id, UpdateTripRequest $request): JsonResponse
    {
        $trip = Trip::findOrFail($id);
        $trip->update($request->validated());

        return $this->successResponse(
            new TripResource($trip->fresh()),
            'Trip berhasil diperbarui.'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $trip = Trip::findOrFail($id);
        $trip->delete();

        return $this->successResponse(null, 'Trip berhasil dihapus.');
    }
}
