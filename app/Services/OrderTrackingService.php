<?php

namespace App\Services;

use App\Repositories\Contracts\OrderTrackingRepositoryInterface;

class OrderTrackingService
{
    protected $trackingRepository;

    public function __construct(OrderTrackingRepositoryInterface $trackingRepository)
    {
        $this->trackingRepository = $trackingRepository;
    }

    public function getTrackingData(string $keyword)
    {
        $order = $this->trackingRepository->findByCodeOrPhone($keyword);

        if (!$order) {
            return null;
        }

        // Return a clean array optimized for the UI
        return [
            'order_code' => $order->order_code,
            'customer_name' => $order->customer_name,
            'service_name' => $order->items->count() > 0 ? $order->items->pluck('service_name')->implode(', ') : $order->service_name,
            'weight' => $order->items->count() > 0 ? $order->items->sum('qty') : $order->weight,
            'status' => $order->status,
            'total_price' => $order->total_price,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at->format('d M Y, H:i'),
            'estimated_finish' => $order->estimated_finish ? \Carbon\Carbon::parse($order->estimated_finish)->format('d M Y, H:i') : null,
        ];
    }
}
