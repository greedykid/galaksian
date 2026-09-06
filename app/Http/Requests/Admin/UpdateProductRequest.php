<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductAvailability;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $productId = $this->route('id') ?? $this->route('product');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$productId],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,'.$productId],
            'description' => ['nullable', 'string'],
            'brand_id' => ['sometimes', 'required', 'integer', 'exists:brands,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'origin_country' => ['nullable', 'string', 'in:ID,JP'],
            'currency' => ['nullable', 'string', 'in:IDR,JPY'],
            'price' => ['sometimes', 'required', 'integer', 'min:0'],
            'discount_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'availability_type' => ['sometimes', 'required', new Enum(ProductAvailability::class)],
            'is_active' => ['nullable', 'boolean'],
            'is_flash_sale' => ['nullable', 'boolean'],
            'flash_sale_start_at' => ['nullable', 'date'],
            'flash_sale_end_at' => ['nullable', 'date'],
            'weight_gram' => ['nullable', 'integer', 'min:0'],
            'length_cm' => ['nullable', 'integer', 'min:0'],
            'width_cm' => ['nullable', 'integer', 'min:0'],
            'height_cm' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
        ];
    }
}
