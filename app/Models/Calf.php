<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calf extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'farm_id',
        'mother_id',
        'father_id',
        'tag_number',
        'gender',
        'birth_date',
        'birth_weight',
        'health_status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'birth_weight' => 'decimal:2',
        'gender' => \App\Enums\CalfGender::class,
        'health_status' => \App\Enums\HealthStatus::class, // Assuming you have a HealthStatus enum
    ];

    /**
     * Get the farm that owns the calf.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the mother animal for the calf.
     */
    public function mother(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    /**
     * Get the father animal for the calf.
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'father_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
