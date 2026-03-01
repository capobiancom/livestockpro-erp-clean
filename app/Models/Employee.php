<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Farm;
use App\Models\User;
use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    use HasFactory;


    protected $fillable = [
        'employee_code',
        'farm_id',
        'user_id',
        'department_id',
        'designation_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'join_date',
        'employment_type',
        'salary_type',
        'status',
        'bonus',
        'festival_bonus',
        'performance_incentive',
        'tax_amount',
        'loan_deduction',
        'other_deductions',
        'user_email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new FarmScope());

        static::creating(function ($employee) {
            $employee->password = Hash::make($employee->password);
        });

        static::updating(function ($employee) {
            if ($employee->isDirty('password')) {
                $employee->password = Hash::make($employee->password);
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_user_id', 'id');
    }

    public function farmUser(): BelongsTo
    {

        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class)
            ->where('effective_from', '<=', now())
            ->orderByDesc('effective_from');
    }
}
