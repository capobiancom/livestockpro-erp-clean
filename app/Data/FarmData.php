<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class FarmData extends Data
{
    public function __construct(
        public string $name,
        public ?string $code = null,
        public ?string $address = null,
        public ?string $contactName = null,
        public ?string $contactPhone = null,
    ) {}
}
