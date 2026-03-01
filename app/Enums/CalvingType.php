<?php

namespace App\Enums;

enum CalvingType: string
{
    case Normal = 'normal';
    case Assisted = 'assisted';
    case CSection = 'c_section';
}
