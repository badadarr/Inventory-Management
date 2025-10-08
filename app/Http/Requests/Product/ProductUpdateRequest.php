<?php

namespace App\Http\Requests\Product;

use App\Enums\Product\ProductFieldsEnum;
use App\Enums\Product\ProductStatusEnum;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\UnitType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
            ProductFieldsEnum::CATEGORY_ID->value   => [
                "bail",
                "required",
                "integer",
                Rule::exists((new Category())->getTable(), 'id')
            ],
            ProductFieldsEnum::SUPPLIER_ID->value   => [
                "bail",
                "nullable",
                "integer",
                Rule::exists((new Supplier())->getTable(), 'id')
            ],
            ProductFieldsEnum::NAME->value          => ["required", "string", "max:255"],
            ProductFieldsEnum::BAHAN->value         => ["nullable", "string", "max:255"],
            ProductFieldsEnum::GRAMATUR->value      => ["nullable", "string", "max:255"],
            ProductFieldsEnum::ALAMAT_PENGIRIMAN->value => ["nullable", "string"],
            ProductFieldsEnum::PRODUCT_CODE->value  => ["nullable", "string", "max:255"],
            ProductFieldsEnum::BUYING_PRICE->value  => ["required", "numeric"],
            ProductFieldsEnum::SELLING_PRICE->value => ["required", "numeric", "gt:0"],
            ProductFieldsEnum::UNIT_TYPE_ID->value  => [
                "required",
                "integer",
                Rule::exists((new UnitType())->getTable(), 'id')
            ],
            ProductFieldsEnum::QUANTITY->value      => ["required", "numeric", "gte:0"],
            ProductFieldsEnum::REORDER_LEVEL->value => ["nullable", "numeric", "gte:0"],
            ProductFieldsEnum::KETERANGAN_TAMBAHAN->value => ["nullable", "string"],
            ProductFieldsEnum::PHOTO->value         => ["nullable", "file", "mimes:jpg,jpeg,png,gif,svg", "max:1024"],
            ProductFieldsEnum::STATUS->value        => ["required", "string", Rule::in(ProductStatusEnum::values())],
            
            // Product sizes validation (dynamic array)
            'sizes'                    => ["nullable", "array", "min:1"],
            'sizes.*.size_name'        => ["nullable", "string", "max:100"],
            'sizes.*.ukuran_potongan'  => ["required", "string", "max:100"],
            'sizes.*.ukuran_plano'     => ["nullable", "string", "max:100"],
            'sizes.*.width'            => ["nullable", "numeric", "gte:0"],
            'sizes.*.height'           => ["nullable", "numeric", "gte:0"],
            'sizes.*.plano_width'      => ["nullable", "numeric", "gte:0"],
            'sizes.*.plano_height'     => ["nullable", "numeric", "gte:0"],
            'sizes.*.notes'            => ["nullable", "string"],
            'sizes.*.is_default'       => ["nullable", "boolean"],
            'sizes.*.sort_order'       => ["nullable", "integer", "gte:0"],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sizes.*.ukuran_potongan.required' => 'Ukuran potongan wajib diisi untuk setiap size.',
            'sizes.*.ukuran_potongan.max'      => 'Ukuran potongan maksimal 100 karakter.',
            'sizes.*.width.numeric'            => 'Lebar harus berupa angka.',
            'sizes.*.height.numeric'           => 'Tinggi harus berupa angka.',
            'sizes.*.plano_width.numeric'      => 'Lebar plano harus berupa angka.',
            'sizes.*.plano_height.numeric'     => 'Tinggi plano harus berupa angka.',
            'sizes.min'                        => 'Minimal harus ada 1 ukuran produk.',
        ];
    }
}
