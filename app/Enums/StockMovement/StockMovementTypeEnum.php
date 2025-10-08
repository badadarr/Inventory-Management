<?php

namespace App\Enums\StockMovement;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum StockMovementTypeEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case IN = 'in';
    case OUT = 'out';

    public static function labels(): array
    {
        return [
            self::IN->value => 'In (Stock Masuk)',
            self::OUT->value => 'Out (Stock Keluar)',
        ];
    }
}
