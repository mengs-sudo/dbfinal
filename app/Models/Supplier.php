<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'phone',
        'email',
        'state',
        'city',
        'address',
        'balance',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public static function generateCode()
    {
        $lastSupplier = self::orderBy('id', 'desc')->first();
        $number = $lastSupplier ? intval(substr($lastSupplier->supplier_code, 3)) + 1 : 1;
        return 'SUP' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
