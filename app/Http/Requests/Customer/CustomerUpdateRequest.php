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
            "nama_sales" => ["nullable", "string", "max:255"],
            "nama_owner" => ["nullable", "string", "max:255"],
            "email"   => [
                "required",
                "email",
                "max:255",
                Rule::unique((new Customer())->getTable())->ignore($this->customer)
            ],
            "phone"   => ["required", "string", "max:255"],
            "address" => ["nullable", "string"],
            "bulan_join" => ["nullable", "string", "max:255"],
            "tahun_join" => ["nullable", "string", "max:255"],
            "status_customer" => ["nullable", "string", "in:new,repeat"],
            "status_komisi" => ["nullable", "string", "max:255"],
            "harga_komisi_standar" => ["nullable", "numeric"],
            "harga_komisi_ekstra" => ["nullable", "numeric"],
            "photo"   => ["nullable", "file", "mimes:jpg,jpeg,png,gif,svg", "max:1024"],
        ];
    }
}
