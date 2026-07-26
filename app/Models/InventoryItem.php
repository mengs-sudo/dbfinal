<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_code',
        'item_name',
        'category_id',
        'quantity',
        'unit_cost',
        'selling_price',
        'reorder_level',
        'image',
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function hasVariants()
    {
        return $this->variants->isNotEmpty();
    }

    /**
     * Total dollar worth of this item's current stock (quantity * unit cost).
     * This is what the inventory valuation report sums across all items.
     */
    public function getInventoryValueAttribute()
    {
        return round($this->quantity * $this->unit_cost, 2);
    }

    public static function generateCode()
    {
        $lastItem = self::orderBy('id', 'desc')->first();
        $number = $lastItem ? intval(substr($lastItem->item_code, 3)) + 1 : 1;
        return 'ITM' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}