<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\CheckupResult; // Import the enum
use App\Scopes\FarmScope;

class PregnancyCheckup extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'pregnancy_id',
        'checkup_date',
        'checkup_result',
        'observations',
        'checked_by',
        'user_id',
        'farm_id',
    ];

    protected $casts = [
        'checkup_date' => 'datetime',
        'checkup_result' => CheckupResult::class,
    ];

    public function pregnancy()
    {
        return $this->belongsTo(Pregnancy::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
