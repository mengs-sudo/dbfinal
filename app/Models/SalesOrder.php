<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'sales_number',
        'customer_id',
        'sales_date',
        'total_amount',
        'paid_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'sales_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCode()
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $number = $lastOrder ? intval(substr($lastOrder->sales_number, 2)) + 1 : 1;
        return 'SO' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
