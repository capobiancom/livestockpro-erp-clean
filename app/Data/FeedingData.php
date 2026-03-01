<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FeedingData extends Data
{
    public function __construct(
        public int $animalId,
        public ?int $feedTypeId = null,
        public ?float $quantity = null,
        public ?string $unit = 'kg',
        public ?string $fedAt = null,
        public ?int $staffId = null,
        public ?string $method = null,
        public ?string $notes = null,
        public ?float $cost = null,
    ) {}
}
