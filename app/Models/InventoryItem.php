<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_code',
        'item_name',
        'category',
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

    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public static function generateCode()
    {
        $lastItem = self::orderBy('id', 'desc')->first();
        $number = $lastItem ? intval(substr($lastItem->item_code, 3)) + 1 : 1;
        return 'ITM' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
