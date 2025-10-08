<?php

namespace App\Enums\Product;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum ProductFieldsEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ID                = 'id';
    case CATEGORY_ID       = 'category_id';
    case SUPPLIER_ID       = 'supplier_id'; // nullable
    case UNIT_TYPE_ID      = 'unit_type_id';
    case PRODUCT_CODE      = 'product_code'; // nullable
    case NAME              = 'name';
    case BAHAN             = 'bahan'; // nullable
    case GRAMATUR          = 'gramatur'; // nullable
    case ALAMAT_PENGIRIMAN = 'alamat_pengiriman'; // nullable
    case BUYING_PRICE      = 'buying_price';
    case SELLING_PRICE     = 'selling_price';
    case QUANTITY          = 'quantity';
    case REORDER_LEVEL     = 'reorder_level'; // v2 addition
    case KETERANGAN_TAMBAHAN = 'keterangan_tambahan'; // v2 addition, nullable
    case PHOTO             = 'photo';
    case STATUS            = 'status';
    case CREATED_AT        = 'created_at';

    public static function labels(): array
    {
        return [
            self::ID->value                => "Id",
            self::CATEGORY_ID->value       => "Category ID",
            self::SUPPLIER_ID->value       => "Supplier ID",
            self::UNIT_TYPE_ID->value      => "Unit Type",
            self::PRODUCT_CODE->value      => "Product Code",
            self::NAME->value              => "Name",
            self::BAHAN->value             => "Bahan",
            self::GRAMATUR->value          => "Gramatur",
            self::ALAMAT_PENGIRIMAN->value => "Alamat Pengiriman",
            self::BUYING_PRICE->value      => "Buying Price",
            self::SELLING_PRICE->value     => "Selling Price",
            self::QUANTITY->value          => "Quantity",
            self::REORDER_LEVEL->value     => "Reorder Level",
            self::KETERANGAN_TAMBAHAN->value => "Keterangan Tambahan",
            self::PHOTO->value             => "Photo",
            self::STATUS->value            => "Status",
        ];
    }
}
