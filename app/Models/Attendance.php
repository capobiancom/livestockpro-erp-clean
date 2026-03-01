<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\FarmScope;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'farm_id',
        'user_id',
        'date',
        'check_in',
        'check_out',
        'working_minutes',
        'overtime_minutes',
        'status',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
        // 'check_in' => 'datetime', // Remove datetime casting
        // 'check_out' => 'datetime', // Remove datetime casting
        'working_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'status' => \App\Enums\AttendanceStatus::class,
        'source' => \App\Enums\AttendanceSource::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new FarmScope);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
