<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logistic extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'reference',
        'vehicle',
        'driver',
        'purpose',
        'from_location',
        'to_location',
        'departure_at',
        'arrival_at',
        'animals_count',
        'animal_ids',
        'cost',
        'notes',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
        'animal_ids' => 'array',
        'cost' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
