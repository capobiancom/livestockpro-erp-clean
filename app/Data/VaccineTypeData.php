<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class VaccineTypeData extends Data
{
    public function __construct(
        public ?int $farm_id = null,
        public string $name,
        public ?string $manufacturer = null,
        public ?string $dose = null,
        public ?int $doses_per_animal = null,
        public ?string $route = null,
        public ?string $notes = null,
    ) {}
}
