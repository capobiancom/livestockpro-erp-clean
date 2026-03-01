<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedingRecord extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'animal_id',
        'feeding_date',
        'feeding_time',
        'animal_id',
        'group_id',
        'notes',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'feeding_date' => 'datetime',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }


    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedingItems()
    {
        return $this->hasMany(FeedingItem::class);
    }

    public function group()
    {
        return $this->belongsTo(Herd::class, 'group_id');
    }
}
