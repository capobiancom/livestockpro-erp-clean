<?php

namespace App\Enums;

enum AttendanceSource: string
{
    case Manual = 'manual';
    case Biometric = 'biometric';
    case Mobile = 'mobile';
}
