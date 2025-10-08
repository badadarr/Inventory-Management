<?php

namespace App\Enums\Employee;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum EmployeeStatusEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case ACTIVE = 'active';
    case RESIGNED = 'resigned';

    public static function labels(): array
    {
        return [
            self::ACTIVE->value => 'Active',
            self::RESIGNED->value => 'Resigned',
        ];
    }

    public static function default(): string
    {
        return self::ACTIVE->value;
    }
}
