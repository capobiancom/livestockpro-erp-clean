<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseTreatmentMedication extends Model
{
    use HasFactory;


    protected $fillable = [
        'disease_treatment_id',
        'medicine_id',
        'farm_id',
        'dose',
        'frequency',
        'duration_days',
        'status',
        'started_at',
        'ended_at',
        'qty',
        'unit_cost',
        'total_cost',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function diseaseTreatment(): BelongsTo
    {
        return $this->belongsTo(DiseaseTreatment::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
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
