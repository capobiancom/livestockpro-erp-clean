<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'purchase_id',
        'item_type',
        'item_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'unit_price',
        'sub_total',
        'user_id',
    ];

    protected $casts = [
        'batch_no' => 'string',
        'expiry_date' => 'date',
        'quantity' => 'float',
        'unit_price' => 'float',
        'sub_total' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
