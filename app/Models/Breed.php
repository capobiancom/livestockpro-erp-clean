<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Breed extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'name',
        'code',
        'description',
        'characteristics',
        'origin',
        'animal_type',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'characteristics' => 'array',
    ];

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
