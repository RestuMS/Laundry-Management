<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|string|in:Reguler,Member,VIP',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap pelanggan wajib diisi.',
            'full_name.max' => 'Nama pelanggan maksimal 255 karakter.',
            'phone.max' => 'Nomor HP maksimal 20 karakter.',
            'status.required' => 'Status pelanggan wajib dipilih.',
            'status.in' => 'Status pelanggan harus Reguler, Member, atau VIP.',
        ];
    }
}
