<?php

namespace App\Http\Requests\Order;

use App\Enums\Core\AmountTypeEnum;
use App\Enums\Order\OrderFieldsEnum;
use App\Enums\Transaction\TransactionPaidThroughEnum;
use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            OrderFieldsEnum::CUSTOMER_ID->value    => [
                "bail",
                "nullable",
                "integer",
                Rule::exists((new Customer())->getTable(), 'id')
            ],
            OrderFieldsEnum::SALES_ID->value       => ["nullable", "integer", Rule::exists('sales', 'id')],
            OrderFieldsEnum::TANGGAL_PO->value     => ["nullable", "date"],
            OrderFieldsEnum::TANGGAL_KIRIM->value  => ["nullable", "date"],
            OrderFieldsEnum::JENIS_BAHAN->value    => ["nullable", "string", "max:255"],
            OrderFieldsEnum::GRAMASI->value        => ["nullable", "string", "max:255"],
            OrderFieldsEnum::VOLUME->value         => ["nullable", "integer"],
            OrderFieldsEnum::HARGA_JUAL_PCS->value => ["nullable", "numeric"],
            OrderFieldsEnum::JUMLAH_CETAK->value   => ["nullable", "integer"],
            OrderFieldsEnum::CATATAN->value        => ["nullable", "string"],
            OrderFieldsEnum::PAID->value           => ["nullable", "numeric"],
            "paid_through"                         => ["required", "string", Rule::in(TransactionPaidThroughEnum::values())],
            "custom_discount"                      => ["nullable", "array"],
            "custom_discount.discount"             => ["required_with:custom_discount", "numeric", "gte:0"],
            "custom_discount.discount_type"        => ["required_with:custom_discount", "string", Rule::in(AmountTypeEnum::values())],
            
            // Order items validation
            "order_items"                          => ["required", "array", "min:1"],
            "order_items.*.product_id"             => ["required", "integer", "exists:products,id"],
            "order_items.*.quantity"               => ["required", "integer", "min:1"],
            "order_items.*.price"                  => ["required", "numeric", "min:0"],
        ];
    }
}
