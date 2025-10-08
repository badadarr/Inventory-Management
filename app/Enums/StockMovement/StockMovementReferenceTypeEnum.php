<?php

namespace App\Enums\StockMovement;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum StockMovementReferenceTypeEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case PURCHASE_ORDER = 'purchase_order';
    case SALES_ORDER = 'sales_order';
    case ADJUSTMENT = 'adjustment';

    public static function labels(): array
    {
        return [
            self::PURCHASE_ORDER->value => 'Purchase Order',
            self::SALES_ORDER->value => 'Sales Order',
            self::ADJUSTMENT->value => 'Adjustment',
        ];
    }
}
