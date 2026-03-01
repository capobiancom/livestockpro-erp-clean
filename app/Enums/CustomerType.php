<?php

namespace App\Enums;

enum CustomerType: string
{
    case Individual = 'individual';
    case Business = 'business';
    case MilkBuyer = 'milk_buyer';
    case AnimalBuyer = 'animal_buyer';
    case Wholesaler = 'wholesaler';

    public function label(): string
    {
        return match ($this) {
            self::Individual => 'Individual',
            self::Business => 'Business',
            self::MilkBuyer => 'Milk Buyer',
            self::AnimalBuyer => 'Animal Buyer',
            self::Wholesaler => 'Wholesaler',
        };
    }
}
