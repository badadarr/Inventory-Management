<?php

namespace App\Enums\Order;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum OrderStatusEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::COMPLETED->value => 'Completed',
            self::CANCELLED->value => 'Cancelled',
        ];
    }

    public static function default(): string
    {
        return self::PENDING->value;
    }
}
