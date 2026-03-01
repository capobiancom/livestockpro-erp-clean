<?php

namespace App\Enums;

enum AnimalType: string
{
    case Cattle = 'cattle';
    case Calf = 'calf';
    case Sheep = 'sheep';
    case Goat = 'goat';
    case Other = 'other';
}
