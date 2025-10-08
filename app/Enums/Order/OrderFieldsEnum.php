<?php

namespace App\Enums\Order;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum OrderFieldsEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ID             = 'id';
    case CUSTOMER_ID    = 'customer_id';
    case SALES_ID       = 'sales_id';
    case ORDER_NUMBER   = 'order_number';
    case TANGGAL_PO     = 'tanggal_po';
    case TANGGAL_KIRIM  = 'tanggal_kirim';
    case JENIS_BAHAN    = 'jenis_bahan';
    case GRAMASI        = 'gramasi';
    case VOLUME         = 'volume';
    case HARGA_JUAL_PCS = 'harga_jual_pcs';
    case JUMLAH_CETAK   = 'jumlah_cetak';
    case SUB_TOTAL      = 'sub_total';
    case TAX_TOTAL      = 'tax_total';
    case DISCOUNT_TOTAL = 'discount_total';
    case CHARGE         = 'charge';
    case TOTAL          = 'total';
    case PAID           = 'paid';
    case DUE            = 'due';
    case STATUS         = 'status';
    case CATATAN        = 'catatan';
    case CREATED_BY     = 'created_by';
    case CREATED_AT     = 'created_at';
    case UPDATED_AT     = 'updated_at';

    public static function labels(): array
    {
        return [
            self::ID->value             => "Id",
            self::CUSTOMER_ID->value    => "Customer ID",
            self::SALES_ID->value       => "Sales Person",
            self::ORDER_NUMBER->value   => "Order Number",
            self::TANGGAL_PO->value     => "Tanggal PO",
            self::TANGGAL_KIRIM->value  => "Tanggal Kirim",
            self::JENIS_BAHAN->value    => "Jenis Bahan",
            self::GRAMASI->value        => "Gramasi",
            self::VOLUME->value         => "Volume",
            self::HARGA_JUAL_PCS->value => "Harga Jual per Pcs",
            self::JUMLAH_CETAK->value   => "Jumlah Cetak",
            self::SUB_TOTAL->value      => "Sub Total",
            self::TAX_TOTAL->value      => "Total Tax",
            self::DISCOUNT_TOTAL->value => "Total Discount",
            self::CHARGE->value         => "Charge",
            self::TOTAL->value          => "Total",
            self::PAID->value           => "Paid",
            self::DUE->value            => "Due",
            self::STATUS->value         => "Status",
            self::CATATAN->value        => "Catatan",
            self::CREATED_BY->value     => "Created By",
            self::CREATED_AT->value     => "Created At",
            self::UPDATED_AT->value     => "Updated At",
        ];
    }
}
