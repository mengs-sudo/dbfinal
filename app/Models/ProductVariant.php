<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'variant_name',
        'sku',
        'quantity',
        'reorder_level',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }
}