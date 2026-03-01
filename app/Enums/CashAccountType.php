<?php

namespace App\Enums;

enum CashAccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Bank => 'Bank',
            self::Mobile => 'Mobile Banking',
        };
    }
}
