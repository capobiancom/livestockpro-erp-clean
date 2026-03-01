<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FixedAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_id',
        'user_id',
        'name',
        'asset_type',
        'purchase_value',
        'purchase_date',
        'useful_life_years',
        'depreciation_method',
        'status',
        'location',
        'serial_number',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_value' => 'float',
        'useful_life_years' => 'integer',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAnnualDepreciationAttribute(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }
        return round($this->purchase_value / $this->useful_life_years, 2);
    }

    public function getCurrentBookValueAttribute(): float
    {
        $yearsElapsed = $this->purchase_date
            ? min(now()->diffInDays($this->purchase_date) / 365, $this->useful_life_years)
            : 0;
        $depreciated = $this->annual_depreciation * $yearsElapsed;
        return max(0, round($this->purchase_value - $depreciated, 2));
    }
}
