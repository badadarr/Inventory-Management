<?php

namespace App\Enums\Order;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum OrderFiltersEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ID            = 'id';
    case CUSTOMER_ID   = 'customer_id';
    case SALES_ID      = 'sales_id';
    case ORDER_NUMBER  = 'order_number';
    case SUB_TOTAL     = 'sub_total';
    case TOTAL         = 'total';
    case DUE           = 'due';
    case STATUS        = 'status';
    case TANGGAL_PO    = 'tanggal_po';
    case TANGGAL_KIRIM = 'tanggal_kirim';
    case CREATED_AT    = 'created_at';

    public static function labels(): array
    {
        return [
            self::ID->value            => "Id",
            self::CUSTOMER_ID->value   => "Customer",
            self::SALES_ID->value      => "Sales Person",
            self::ORDER_NUMBER->value  => "Order Number",
            self::SUB_TOTAL->value     => "Sub Total",
            self::TOTAL->value         => "Total",
            self::DUE->value           => "Due",
            self::STATUS->value        => "Status",
            self::TANGGAL_PO->value    => "Tanggal PO",
            self::TANGGAL_KIRIM->value => "Tanggal Kirim",
            self::CREATED_AT->value    => "Created At",
        ];
    }
}
