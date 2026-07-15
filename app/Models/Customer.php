<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'customer_name',
        'phone',
        'email',
        'state',
        'city',
        'address',
        'balance',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public static function generateCode()
    {
        $lastCustomer = self::orderBy('id', 'desc')->first();
        $number = $lastCustomer ? intval(substr($lastCustomer->customer_code, 3)) + 1 : 1;
        return 'CUS' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
