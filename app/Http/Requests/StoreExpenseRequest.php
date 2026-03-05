<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengeluaran wajib diisi.',
            'name.max' => 'Nama pengeluaran maksimal 255 karakter.',
            'amount.required' => 'Jumlah pengeluaran wajib diisi.',
            'amount.min' => 'Jumlah pengeluaran tidak boleh negatif.',
            'date.required' => 'Tanggal pengeluaran wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
