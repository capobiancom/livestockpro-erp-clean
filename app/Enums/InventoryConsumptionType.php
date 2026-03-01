<?php

namespace App\Enums;

enum InventoryConsumptionType: string
{
    case FIFO = 'FIFO';
    case FEFO = 'FEFO';
}
