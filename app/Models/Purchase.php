<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = ['supplier_id', 'invoice_number', 'purchased_at', 'notes', 'total_amount', 'paid_amount', 'discount', 'discount_type', 'tax', 'tax_percentage', 'farm_id', 'user_id'];

    protected $casts = [
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'discount' => 'float',
        'discount_type' => 'string',
        'tax' => 'float',
        'tax_percentage' => 'float',
        'purchased_at' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplierPayments()
    {
        return $this->morphMany(SupplierPayment::class, 'purchase_source');
    }
}
