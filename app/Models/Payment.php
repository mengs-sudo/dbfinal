<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_number',
        'type',
        'entity_name',
        'purchase_order_id',
        'payment_date',
        'amount',
        'payment_method',
        'status',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCode()
    {
        $lastPayment = self::orderBy('id', 'desc')->first();
        $number = $lastPayment ? intval(substr($lastPayment->payment_number, 3)) + 1 : 1;
        return 'PAY' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
