<?php

namespace App\Http\Requests\User;

use App\Models\Order;
use App\Models\Product;
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
            'replacement_product_id' => ['required_if:resolution,replace', 'nullable', 'integer', 'exists:products,id'],
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
            if ($item->refund_status !== null) {
                $validator->errors()->add('resolution', 'Item ini sudah pernah diproses.');
            }

            if ($this->input('resolution') === 'replace') {
                $product = Product::active()->find($this->input('replacement_product_id'));
                if (! $product) {
                    $validator->errors()->add('replacement_product_id', 'Produk pengganti tidak tersedia.');
                }
            }

            if ($this->input('resolution') === 'refund' && $this->filled('amount') && (int) $this->input('amount') !== (int) $item->subtotal) {
                $validator->errors()->add('amount', 'Nominal refund tidak valid.');
            }
        });
    }
}
