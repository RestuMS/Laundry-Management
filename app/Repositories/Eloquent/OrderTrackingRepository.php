<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderTrackingRepositoryInterface;

class OrderTrackingRepository implements OrderTrackingRepositoryInterface
{
    public function findByCodeOrPhone(string $keyword)
    {
        return Order::where('order_code', $keyword)
                    ->orWhere('customer_phone', $keyword)
                    ->first();
    }
}
