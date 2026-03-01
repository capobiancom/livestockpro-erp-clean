<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VaccineType extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = ['name', 'manufacturer', 'dose', 'doses_per_animal', 'route', 'notes', 'farm_id', 'user_id'];

    public function vaccinations()
    {
        return $this->hasMany(VaccinationRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
