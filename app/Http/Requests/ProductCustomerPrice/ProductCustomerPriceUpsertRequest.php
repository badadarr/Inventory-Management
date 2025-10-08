<?php

namespace App\Http\Requests\ProductCustomerPrice;

use Illuminate\Foundation\Http\FormRequest;

class ProductCustomerPriceUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'custom_price' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product harus dipilih',
            'product_id.exists' => 'Product tidak ditemukan',
            'customer_id.required' => 'Customer harus dipilih',
            'customer_id.exists' => 'Customer tidak ditemukan',
            'custom_price.required' => 'Custom price harus diisi',
            'custom_price.min' => 'Custom price tidak boleh negatif',
        ];
    }
}
