<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class PayrollItemData extends Data
{
    public function __construct(
        public int $payrollRunId,
        public int $employeeId,
        public float $basicSalary,
        public float $houseAllowance,
        public float $medicalAllowance,
        public float $transportAllowance,
        public float $overtimeHours,
        public float $overtimeRate,
        public float $overtimeAmount,
        public float $grossSalary,
        public float $deductions,
        public float $netSalary,
    ) {}
}
