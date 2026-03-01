<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class HerdData extends Data
{
    public function __construct(
        public int $farm_id,
        public string $name,
        public ?string $code = null,
        public ?string $description = null,
    ) {}
}
