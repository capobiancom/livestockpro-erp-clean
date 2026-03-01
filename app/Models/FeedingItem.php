<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'feeding_record_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function feedingRecord()
    {
        return $this->belongsTo(FeedingRecord::class);
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
