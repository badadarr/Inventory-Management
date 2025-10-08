<?php

namespace App\Enums\Customer;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum CustomerStatusEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case BARU = 'baru';
    case REPEAT = 'repeat';

    public static function labels(): array
    {
        return [
            self::BARU->value => 'Baru',
            self::REPEAT->value => 'Repeat',
        ];
    }

    public static function default(): string
    {
        return self::BARU->value;
    }
}
