<?php

namespace App\Enums\PurchaseOrder;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum PurchaseOrderStatusEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case PENDING = 'pending';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::RECEIVED->value => 'Received',
            self::CANCELLED->value => 'Cancelled',
        ];
    }

    public static function default(): string
    {
        return self::PENDING->value;
    }
}
