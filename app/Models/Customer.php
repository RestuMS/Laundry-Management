<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['full_name', 'phone', 'address', 'status', 'total_orders', 'notes'];

    /**
     * Get all orders for this customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get total spending across all orders.
     */
    public function getTotalSpendingAttribute(): int
    {
        return $this->orders()->sum('total_price');
    }
}
