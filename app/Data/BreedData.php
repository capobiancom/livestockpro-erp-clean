<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class BreedData extends Data
{
    public function __construct(
        public string $name,
        public ?string $code = null,
        public ?string $description = null,
        public ?array $characteristics = null,
    ) {}
}
