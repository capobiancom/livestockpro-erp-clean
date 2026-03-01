<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class InventoryItemData extends Data
{
    public function __construct(
        public string $name,
        public ?string $sku = null,
        public ?int $inventory_category_id = null,
        public ?string $category = null,
        public ?int $farm_id = null,
        public ?int $user_id = null,
        public ?float $quantity = null,
        public ?string $unit = null,
        public ?float $min_quantity = null,
        public ?float $unit_cost = null,
        public ?int $supplier_id = null,
        public ?string $notes = null,
    ) {}
}
