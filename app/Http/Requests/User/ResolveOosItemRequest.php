<?php

namespace App\Http\Requests\User;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveOosItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sudah dicek kepemilikan order via Gate::authorize('view') di controller.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'resolution' => ['required', Rule::in(['refund', 'replace'])],
            // amount TIDAK boleh dikirim user secara bebas; backend menghitung ulang
            // dari snapshot order. Jika dikirim, nilainya WAJIB sama dengan subtotal item
            // yang sah. Dihitung ulang di OrderService::resolveOosItem.
            'amount' => ['nullable', 'integer', 'min:1'],
            'replacement_name' => ['required_if:resolution,replace', 'nullable', 'string', 'max:255'],
            // replacement_price juga TIDAK boleh dipercaya; backend menghitung ulang
            // dari Product yang sah di DB. Dihitung ulang di OrderService.
            'replacement_price' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $orderId = $this->route('id');
            $itemId = $this->route('itemId');
            $order = Order::find($orderId);
            $item = $order?->items()->find($itemId);

            if (! $item) {
                return;
            }

            // Validasi nilai amount terhadap snapshot order (jangan percaya klien).
            // Backend menghitung ulang dari $item->subtotal; klien hanya boleh
            // mengirim nilai yang SAMA dengan subtotal item.
            if ($this->input('resolution') === 'refund' && $this->filled('amount') && (int) $this->input('amount') !== (int) $item->subtotal) {
                $validator->errors()->add('amount', 'Nominal refund tidak valid.');
            }
        });
    }
}
