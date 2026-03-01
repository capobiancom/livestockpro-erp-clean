<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PurchaseData extends BaseData
{
    public function __construct(
        public ?int $supplierId = null,
        public ?string $invoiceNumber = null,
        public ?string $purchasedAt = null,
        public ?string $notes = null,
        public ?float $totalAmount = null,
        public ?float $discount = null,
        public ?float $tax = null,
        public ?array $items = [],
    ) {}
}

class PurchaseItemData extends BaseData
{
    public function __construct(
        public ?int $id = null,
        public string $itemType,
        public int $itemId,
        public float $quantity,
        public float $unitPrice,
        public float $subTotal,
    ) {}
}
