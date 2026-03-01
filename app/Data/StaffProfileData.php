<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class StaffProfileData extends \App\Data\BaseData
{
    public function __construct(
        public string $firstName,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $position = null,
        public ?int $farmId = null,
        public ?string $hiredAt = null,
        public ?array $metadata = null,
    ) {}
}
