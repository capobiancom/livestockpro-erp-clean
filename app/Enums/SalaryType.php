<?php

namespace App\Enums;

enum SalaryType: string
{
    case Monthly = 'monthly';
    case Daily = 'daily';
    case Hourly = 'hourly';
}
