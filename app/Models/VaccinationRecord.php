<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\VaccinationMedication;
use App\Scopes\FarmScope;

class VaccinationRecord extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'animal_id',
        'disease_id',
        'administered_at',
        'next_due_at',
        'staff_id',
        'notes',
        'farm_id',
        'user_id'
    ]; // Add farm_id to fillable

    protected $casts = [
        'administered_at' => 'datetime',
        'next_due_at' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }


    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }

    public function medications()
    {
        return $this->hasMany(VaccinationMedication::class);
    }
}
