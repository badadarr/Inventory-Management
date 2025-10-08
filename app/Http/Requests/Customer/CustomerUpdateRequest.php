<?php

namespace App\Http\Requests\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
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
            "name"    => ["required", "string", "max:255"],
            "nama_box" => ["nullable", "string", "max:255"],
            "sales_id" => ["nullable", "exists:sales,id"],
            "nama_owner" => ["nullable", "string", "max:255"],
            "email"   => [
                "required",
                "email",
                "max:255",
                Rule::unique((new Customer())->getTable())->ignore($this->customer)
            ],
            "phone"   => ["required", "string", "max:255"],
            "address" => ["nullable", "string"],
            "tanggal_join" => ["nullable", "date"],
            "status_customer" => ["nullable", "string", "in:baru,repeat"],
            "status_komisi" => ["nullable", "string", "max:255"],
            "harga_komisi_standar" => ["nullable", "numeric", "min:0"],
            "harga_komisi_extra" => ["nullable", "numeric", "min:0"],
            "photo"   => ["nullable", "file", "mimes:jpg,jpeg,png,gif,svg", "max:1024"],
        ];
    }
}
