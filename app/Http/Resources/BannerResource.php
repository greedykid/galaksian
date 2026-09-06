<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image_path' => $this->image_path,
            'link_url' => $this->link_url,
            'cta_text' => $this->cta_text,
            'order' => $this->order,
            'is_active' => (bool) $this->is_active,
            'locale' => $this->locale,
            'country_filter' => $this->country_filter,
        ];
    }
}
