<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedType extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'name',
        'category',
        'unit',
        'unit_cost',
        'description',
        'nutrient_info',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'nutrient_info' => 'array',
        'unit_cost' => 'float',
    ];

    public function feedingRecords()
    {
        return $this->hasMany(FeedingRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
