<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'inventory_category_id',
        'quantity',
        'unit',
        'min_quantity',
        'unit_cost',
        'supplier_id',
        'notes',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'float',
        'min_quantity' => 'float',
        'unit_cost' => 'float',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'inventory_category_id');
    }

    public function purchaseItems()
    {
        return $this->morphMany(PurchaseItem::class, 'item');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the FIFO unit price for the inventory item.
     * This method considers 'in' stock movements and applies FIFO logic.
     *
     * @return float
     */
    public function getFifoUnitPriceAttribute(): float
    {
        // Get all 'in' stock movements for this inventory item, ordered by movement_date (FIFO)
        $inMovements = StockMovement::where('item_type', self::class)
            ->where('item_id', $this->id)
            ->where('farm_id', $this->farm_id) // Ensure farm scope
            ->where('movement_type', 'in')
            ->orderBy('movement_date')
            ->orderBy('id') // Secondary sort for consistent FIFO if dates are the same
            ->get();

        foreach ($inMovements as $movement) {
            // Calculate consumed quantity for this specific batch
            $consumedQuantity = StockMovement::where('item_type', self::class)
                ->where('item_id', $this->id)
                ->where('farm_id', $this->farm_id)
                ->where('movement_type', 'out')
                ->where('batch_no', $movement->batch_no)
                ->sum('quantity');

            $availableQuantity = $movement->quantity - $consumedQuantity;

            if ($availableQuantity > 0) {
                // This is the first available batch according to FIFO, return its unit cost
                return $movement->unit_cost;
            }
        }

        // If no stock is available, return 0
        return 0;
    }
}
