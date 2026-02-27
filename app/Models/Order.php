<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'service_name',
        'weight',
        'package_detail',
        'estimated_finish',
        'status',
        'total_price',
        'discount',
        'tax',
        'payment_method',
        'payment_status',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
