<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CalvingType;
use App\Enums\CalfGender;
use App\Enums\CalvingOutcome;
use App\Scopes\FarmScope;

class CalvingRecord extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'farm_id',
        'pregnancy_id',
        'calving_date',
        'calving_type',
        'calves_count',
        'calf_gender',
        'calving_outcome',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'calving_date' => 'date',
        'calving_type' => CalvingType::class,
        'calf_gender' => CalfGender::class,
        'calving_outcome' => CalvingOutcome::class,
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function pregnancy()
    {
        return $this->belongsTo(Pregnancy::class);
    }

    /**
     * Get the calves associated with the calving record.
     */
    public function calves()
    {
        return $this->hasMany(Calf::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
