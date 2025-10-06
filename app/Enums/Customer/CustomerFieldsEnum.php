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
    case NAMA_SALES = 'nama_sales';
    case NAMA_OWNER = 'nama_owner';
    case EMAIL      = 'email';
    case PHONE      = 'phone';
    case ADDRESS    = 'address';
    case BULAN_JOIN = 'bulan_join';
    case TAHUN_JOIN = 'tahun_join';
    case STATUS_CUSTOMER = 'status_customer';
    case STATUS_KOMISI = 'status_komisi';
    case HARGA_KOMISI_STANDAR = 'harga_komisi_standar';
    case HARGA_KOMISI_EKSTRA = 'harga_komisi_ekstra';
    case PHOTO      = 'photo';
    case CREATED_AT = 'created_at';

    public static function labels(): array
    {
        return [
            self::ID->value        => "Id",
            self::NAME->value      => "Name",
            self::NAMA_BOX->value  => "Nama Box",
            self::NAMA_SALES->value => "Nama Sales",
            self::NAMA_OWNER->value => "Nama Owner",
            self::EMAIL->value     => "Email",
            self::PHONE->value     => "Phone",
            self::ADDRESS->value   => "Address",
            self::BULAN_JOIN->value => "Bulan Join",
            self::TAHUN_JOIN->value => "Tahun Join",
            self::STATUS_CUSTOMER->value => "Status Customer",
            self::STATUS_KOMISI->value => "Status Komisi",
            self::HARGA_KOMISI_STANDAR->value => "Harga Komisi Standar",
            self::HARGA_KOMISI_EKSTRA->value => "Harga Komisi Ekstra",
            self::PHOTO->value     => "Photo",
        ];
    }
}
