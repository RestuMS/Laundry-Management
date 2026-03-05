<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'usage_per_kg' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'unit.required' => 'Satuan wajib diisi.',
            'usage_per_kg.required' => 'Pemakaian per Kg wajib diisi.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
        ];
    }
}
