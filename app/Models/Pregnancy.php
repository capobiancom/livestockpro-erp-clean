<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pregnancy extends Model
{
    use HasFactory, SoftDeletes;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'farm_id',
        'animal_id',
        'reproduction_record_id',
        'pregnancy_confirmed_date',
        'expected_gestation_days',
        'expected_calving_date',
        'pregnancy_status',
        'health_notes',
        'user_id',
    ];

    protected $casts = [
        'pregnancy_confirmed_date' => 'date',
        'expected_calving_date' => 'date',
        'expected_gestation_days' => 'integer',
        'pregnancy_status' => \App\Enums\PregnancyStatus::class, // Assuming an Enum for PregnancyStatus
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function reproductionRecord()
    {
        return $this->belongsTo(ReproductionRecord::class);
    }

    public function checkups()
    {
        return $this->hasMany(PregnancyCheckup::class);
    }

    public function calvingRecords()
    {
        return $this->hasMany(CalvingRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
