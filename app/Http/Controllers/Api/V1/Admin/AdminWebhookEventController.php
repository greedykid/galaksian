<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookEventResource;
use App\Models\WebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminWebhookEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = WebhookEvent::latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($eventType = $request->query('event_type')) {
            $query->where('event_type', $eventType);
        }

        if ($search = $request->query('q')) {
            $query->whereLike('event_id', "%{$search}%");
        }

        $perPage = min((int) $request->query('per_page', 20), 100);
        $events = $query->paginate($perPage);

        return $this->successResponse(
            WebhookEventResource::collection($events)->response()->getData(true),
            'Daftar webhook events berhasil diambil.'
        );
    }

    public function show(int $id): JsonResponse
    {
        $event = WebhookEvent::findOrFail($id);

        return $this->successResponse(
            new WebhookEventResource($event),
            'Detail webhook event berhasil diambil.'
        );
    }
}
