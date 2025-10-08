<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'order_number' => 'required|string|max:50|unique:purchase_orders,order_number',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0|lte:total_amount',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:pending,received,cancelled',
            'created_by' => 'required|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Supplier harus dipilih',
            'supplier_id.exists' => 'Supplier tidak ditemukan',
            'order_number.required' => 'Nomor order harus diisi',
            'order_number.unique' => 'Nomor order sudah digunakan',
            'order_date.required' => 'Tanggal order harus diisi',
            'total_amount.required' => 'Total amount harus diisi',
            'total_amount.min' => 'Total amount tidak boleh negatif',
            'paid_amount.lte' => 'Paid amount tidak boleh lebih dari total amount',
        ];
    }
}
