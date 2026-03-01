<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;


    protected $fillable = [
        'farm_id',
        'item_type',
        'item_id',
        'movement_type',
        'source_event_type',
        'source_id',
        'source_type', // This is for the polymorphic relation
        'quantity',
        'unit_cost',
        'batch_no',
        'expiry_date',
        'movement_date',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_cost' => 'float',
        'expiry_date' => 'date',
        'movement_date' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
