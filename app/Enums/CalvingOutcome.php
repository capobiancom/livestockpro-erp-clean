<?php

namespace App\Enums;

enum CalvingOutcome: string
{
    case Successful = 'successful';
    case Stillbirth = 'stillbirth';
    case Complication = 'complication';
    case Expected = 'expected';
}
