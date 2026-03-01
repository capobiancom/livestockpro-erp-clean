<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Treatment extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = ['name', 'instructions', 'farm_id', 'user_id'];

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'medications')
            ->withTimestamps()
            ->withPivot(['dose', 'frequency', 'duration_days']);
    }

    public function suppliers(): HasManyThrough
    {
        return $this->hasManyThrough(Supplier::class, Medicine::class, 'id', 'id', 'id', 'supplier_id');
    }

    public function medications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Medication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
