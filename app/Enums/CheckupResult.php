<?php

namespace App\Enums;

enum CheckupResult: string
{
    case Normal = 'normal';
    case Risk = 'risk';
    case Critical = 'critical';
    case Aborted = 'aborted';
}
