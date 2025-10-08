<?php

namespace App\Enums\User;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum UserRoleEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ADMIN = 'admin';
    case SALES = 'sales';
    case FINANCE = 'finance';
    case WAREHOUSE = 'warehouse';

    public static function labels(): array
    {
        return [
            self::ADMIN->value => 'Admin',
            self::SALES->value => 'Sales',
            self::FINANCE->value => 'Finance',
            self::WAREHOUSE->value => 'Warehouse',
        ];
    }

    public static function default(): string
    {
        return self::ADMIN->value;
    }
}
