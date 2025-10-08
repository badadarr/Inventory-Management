<?php

namespace App\Enums\Transaction;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum TransactionTypeEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case PAYMENT = 'payment';
    case REFUND = 'refund';
    case ADJUSTMENT = 'adjustment';

    public static function labels(): array
    {
        return [
            self::PAYMENT->value => 'Payment',
            self::REFUND->value => 'Refund',
            self::ADJUSTMENT->value => 'Adjustment',
        ];
    }

    public static function default(): string
    {
        return self::PAYMENT->value;
    }
}
