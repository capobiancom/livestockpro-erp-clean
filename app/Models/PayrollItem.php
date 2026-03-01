<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_salary',
        'house_allowance',
        'medical_allowance',
        'transport_allowance',
        'overtime_hours',
        'overtime_rate',
        'overtime_amount',
        'gross_salary',
        'deductions',
        'net_salary',
        'working_days',
        'paid_leave_days',
        'unpaid_leave_days',
        'leave_deduction',
        'bonus',
        'festival_bonus',
        'performance_incentive',
        'tax_amount',
        'loan_deduction',
        'other_deductions',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
