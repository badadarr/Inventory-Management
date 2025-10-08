<?php

namespace App\Enums\Customer;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum CustomerFieldsEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ID         = 'id';
    case NAME       = 'name';
    case NAMA_BOX   = 'nama_box';
    case SALES_ID   = 'sales_id';
    case NAMA_OWNER = 'nama_owner';
    case EMAIL      = 'email';
    case PHONE      = 'phone';
    case ADDRESS    = 'address';
    case TANGGAL_JOIN = 'tanggal_join';
    case STATUS_CUSTOMER = 'status_customer';
    case STATUS_KOMISI = 'status_komisi';
    case HARGA_KOMISI_STANDAR = 'harga_komisi_standar';
    case HARGA_KOMISI_EXTRA = 'harga_komisi_extra';
    case PHOTO      = 'photo';
    case CREATED_AT = 'created_at';

    public static function labels(): array
    {
        return [
            self::ID->value        => "Id",
            self::NAME->value      => "Name",
            self::NAMA_BOX->value  => "Nama Box",
            self::SALES_ID->value  => "Sales Person",
            self::NAMA_OWNER->value => "Nama Owner",
            self::EMAIL->value     => "Email",
            self::PHONE->value     => "Phone",
            self::ADDRESS->value   => "Address",
            self::TANGGAL_JOIN->value => "Tanggal Join",
            self::STATUS_CUSTOMER->value => "Status Customer",
            self::STATUS_KOMISI->value => "Status Komisi",
            self::HARGA_KOMISI_STANDAR->value => "Harga Komisi Standar",
            self::HARGA_KOMISI_EXTRA->value => "Harga Komisi Extra",
            self::PHOTO->value     => "Photo",
        ];
    }
}
