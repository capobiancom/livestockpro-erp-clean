<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MilkRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'date',
        'quantity_liters',
        'staff_id',
        'notes',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'quantity_liters' => 'float',
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
}
