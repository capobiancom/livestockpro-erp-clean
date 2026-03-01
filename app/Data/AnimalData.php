<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class AnimalData extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $animalType = null,
        public ?string $sex = null,
        public ?string $dob = null,
        public ?int $breedId = null,
        public ?int $farmId = null,
        public ?int $herdId = null,
        public ?string $status = 'active',
        public ?float $currentWeightKg = null,
        public ?string $color = null,
        public ?string $acquiredAt = null,
        public ?float $purchasePrice = null,
        public ?int $supplierId = null, // Changed 'source' to 'supplierId'
        public ?array $attributes = null,
        public ?string $notes = null,
        public ?string $image = null,
    ) {}
}
