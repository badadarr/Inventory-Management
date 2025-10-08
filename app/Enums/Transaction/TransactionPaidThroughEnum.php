<?php

namespace App\Enums\Transaction;

use App\Enums\BaseEnumInterface;
use App\Enums\BaseEnumTrait;

enum TransactionPaidThroughEnum: string implements BaseEnumInterface
{
    use BaseEnumTrait;

    case CASH           = 'cash';
    case BANK_TRANSFER  = 'bank_transfer';
    case CREDIT_CARD    = 'credit_card';
    case DEBIT_CARD     = 'debit_card';
    case E_WALLET       = 'e_wallet';
    case QRIS           = 'qris';
    case GIFT_CARD      = 'gift_card';

    public static function labels(): array
    {
        return [
            self::CASH->value          => "Cash",
            self::BANK_TRANSFER->value => "Bank Transfer",
            self::CREDIT_CARD->value   => "Credit Card",
            self::DEBIT_CARD->value    => "Debit Card",
            self::E_WALLET->value      => "E-Wallet (OVO, GoPay, Dana, dll)",
            self::QRIS->value          => "QRIS",
            self::GIFT_CARD->value     => "Gift Card",
        ];
    }
}
