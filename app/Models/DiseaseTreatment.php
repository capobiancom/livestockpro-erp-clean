<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseTreatment extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'health_issue_id',
        'health_event_id',
        //'treatment_name', // Replaced with
        'treatment_id', // Replaced treatment_name with treatment_id
        'farm_id', // Add farm_id
        'description',
        'notes',
        'status',
        'user_id',
    ];

    protected $casts = [
        // 'resolved_at' => 'date', // Removed as it's for health_events table
    ];

    public function healthIssue(): BelongsTo
    {
        return $this->belongsTo(HealthIssue::class);
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'administered_by');
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function healthEvent(): BelongsTo
    {
        return $this->belongsTo(HealthEvent::class);
    }

    public function diseaseTreatmentMedications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DiseaseTreatmentMedication::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
