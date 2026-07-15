<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'sales_order_id',
        'payment_id',
        'receipt_date',
        'amount',
        'payment_method',
        'status',
        'created_by',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCode()
    {
        $lastReceipt = self::orderBy('id', 'desc')->first();
        $number = $lastReceipt ? intval(substr($lastReceipt->receipt_number, 3)) + 1 : 1;
        return 'REC' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
