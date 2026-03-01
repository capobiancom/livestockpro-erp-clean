<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = ['name', 'contact_name', 'phone', 'email', 'address', 'metadata', 'farm_id', 'user_id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
