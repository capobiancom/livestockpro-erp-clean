<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farm extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'code',
        'address',
        'contact_name',
        'contact_phone',
        'metadata',
        'user_id',
        'demo_data_seeded',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function herds()
    {
        return $this->hasMany(Herd::class);
    }

    public function animals()
    {
        return $this->hasMany(Animal::class);
    }

    public function staff()
    {
        return $this->hasMany(StaffProfile::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function chartOfAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class);
    }

    public function subscription(): HasOne
    {
        // Always treat the "current" subscription as the latest one.
        // This must be consistent across admin dashboard display, toggles, and access checks.
        return $this->hasOne(FarmSubscription::class)->latestOfMany('ends_on');
    }
}
