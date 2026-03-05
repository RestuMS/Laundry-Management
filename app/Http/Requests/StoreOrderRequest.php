<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'estimated_finish' => 'nullable|date',
            'total_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Belum Bayar,DP,Lunas',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'total_price.required' => 'Total harga wajib diisi.',
            'total_price.min' => 'Total harga tidak boleh negatif.',
            'payment_status.required' => 'Status pembayaran wajib dipilih.',
            'payment_status.in' => 'Status pembayaran tidak valid.',
            'items.required' => 'Minimal harus ada 1 item layanan.',
            'items.min' => 'Minimal harus ada 1 item layanan.',
            'items.*.service_name.required' => 'Nama layanan wajib diisi.',
            'items.*.qty.required' => 'Jumlah item wajib diisi.',
            'items.*.qty.min' => 'Jumlah item minimal 0.01.',
            'items.*.price.required' => 'Harga per item wajib diisi.',
            'items.*.subtotal.required' => 'Subtotal item wajib diisi.',
        ];
    }
}
