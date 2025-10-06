<?php

namespace App\Enums\User;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum UserRoleEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case SALES = 'sales';
    case WAREHOUSE = 'warehouse';
    case FINANCE = 'finance';

    public static function labels(): array
    {
        return [
            self::SUPER_ADMIN->value => 'Super Admin',
            self::ADMIN->value => 'Admin',
            self::SALES->value => 'Sales',
            self::WAREHOUSE->value => 'Warehouse',
            self::FINANCE->value => 'Finance',
        ];
    }
}
