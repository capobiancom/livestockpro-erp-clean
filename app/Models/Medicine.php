<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'name',
        'description',
        'medicine_group_id',
        'supplier_id',
        'farm_id',
        'quantity',
        'unit',
        'min_quantity',
        'unit_cost',
        'inventory_category_id',
        'sku',
        'user_id',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'inventory_category_id');
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function medicineGroup()
    {
        return $this->belongsTo(MedicineGroup::class);
    }

    public function purchaseItems()
    {
        return $this->morphMany(PurchaseItem::class, 'item');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
