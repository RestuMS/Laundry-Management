<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'service_name.required' => 'Nama layanan wajib diisi.',
            'service_name.max' => 'Nama layanan maksimal 255 karakter.',
            'price.required' => 'Harga layanan wajib diisi.',
            'price.min' => 'Harga layanan tidak boleh negatif.',
            'unit.required' => 'Satuan layanan wajib diisi.',
        ];
    }
}
