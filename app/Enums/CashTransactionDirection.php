<?php

namespace App\Enums;

enum CashTransactionDirection: string
{
    case In = 'in';
    case Out = 'out';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Cash In',
            self::Out => 'Cash Out',
        };
    }
}
