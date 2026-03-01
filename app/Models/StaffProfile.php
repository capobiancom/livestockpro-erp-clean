<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffProfile extends Model
{
    use HasFactory, SoftDeletes;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'position',
        'farm_id',
        'hired_at',
        'metadata',
        'user_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'hired_at' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
