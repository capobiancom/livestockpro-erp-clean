<?php

namespace App\Enums;

enum JournalReferenceType: string
{
    case Sale = 'sale';
    case Purchase = 'purchase';
    case Payroll = 'payroll';
    case Treatment = 'treatment';
}
