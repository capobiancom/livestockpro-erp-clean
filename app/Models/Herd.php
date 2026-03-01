<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Herd extends Model
{
    use HasFactory, SoftDeletes;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'farm_id',
        'name',
        'code',
        'description',
        'user_id'
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
