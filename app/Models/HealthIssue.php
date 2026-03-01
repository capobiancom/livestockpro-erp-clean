<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthIssue extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'animal_id',
        'farm_id',
        'name', // Add 'name' to fillable
        'disease_id',
        'diagnosed_at',
        'severity',
        'symptoms',
        'diagnosis',
        'status',
        'recovered_at',
        'diagnosed_by',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'diagnosed_at' => 'date',
        'recovered_at' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function diagnosedBy(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'diagnosed_by');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(DiseaseTreatment::class);
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
