<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'Cash';
    case BankTransfer = 'Bank Transfer';
    case MobileBanking = 'Mobile Banking';
}
