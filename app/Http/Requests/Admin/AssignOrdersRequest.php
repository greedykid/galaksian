<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'order_ids' => ['sometimes', 'array'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'shipment_id' => ['sometimes', 'required', 'integer', 'exists:shipments,id'],
        ];
    }
}
