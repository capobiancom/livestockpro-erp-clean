<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class PayrollRunData extends Data
{
    public function __construct(
        public ?int $farmId = null,
        public ?int $userId = null,
        public ?int $month = null,
        public ?int $year = null,
        public ?string $status = 'draft',
        public ?string $generatedAt = null,
    ) {}
}
