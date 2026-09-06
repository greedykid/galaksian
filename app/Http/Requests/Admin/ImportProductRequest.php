<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required_without:items', 'file', 'mimes:csv,xlsx,txt,json', 'max:10240'],
            'items' => ['required_without:file', 'array'],
        ];
    }
}
