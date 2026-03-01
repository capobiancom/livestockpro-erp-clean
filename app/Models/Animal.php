<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'tag',
        'name',
        'animal_type',
        'sex',
        'dob',
        'breed_id',
        'farm_id',
        'herd_id',
        'status',
        'current_weight_kg',
        'color',
        'acquired_at',
        'purchase_price',
        'supplier_id', // Change 'source' to 'supplier_id'
        'attributes',
        'notes',
        'image',
        'user_id'
    ];

    protected $casts = [
        'dob' => 'date',
        'acquired_at' => 'date',
        'attributes' => 'array',
        'current_weight_kg' => 'float',
        'purchase_price' => 'float',
        'supplier_id' => 'integer', // Add supplier_id to casts
    ];

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function herd()
    {
        return $this->belongsTo(Herd::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'item');
    }
}
