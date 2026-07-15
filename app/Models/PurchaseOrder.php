<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'purchase_date',
        'total_amount',
        'paid_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCode()
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $number = $lastOrder ? intval(substr($lastOrder->purchase_number, 2)) + 1 : 1;
        return 'PO' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
