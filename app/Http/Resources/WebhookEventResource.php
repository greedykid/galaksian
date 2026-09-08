<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'event_id' => $this->event_id,
            'event_type' => $this->event_type,
            'status' => $this->status,
            'payload' => $this->when($this->isDetailRequest($request), $this->payload),
            'signature' => $this->when($this->isDetailRequest($request), $this->signature),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function isDetailRequest(Request $request): bool
    {
        // Hanya expose payload/signature pada request DETAIL (route show),
        // bukan semua request yang path-nya cocok '*/webhook-events/*'.
        return $request->route()?->getName() === 'admin.webhook-events.show';
    }
}
