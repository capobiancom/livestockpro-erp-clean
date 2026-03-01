<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class VaccinationData extends Data
{
    public function __construct(
        public int $animal_id,
        public ?int $vaccine_type_id = null,
        public ?string $batch_number = null,
        public ?string $administered_at = null,
        public ?string $next_due_at = null,
        public ?int $staff_id = null,
        public ?string $notes = null,
        public ?int $farm_id = null,
    ) {}
}
