<?php

namespace App\Enums\Sales;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum SalesStatusEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
        ];
    }

    public static function default(): string
    {
        return self::ACTIVE->value;
    }
}
