<?php

namespace App\Services;

use App\Repositories\Contracts\OrderTrackingRepositoryInterface;
use App\Models\Order;

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

        // Build the status timeline from status_history
        $statusTimeline = [];
        $history = $order->status_history ?? [];

        foreach (Order::STATUSES as $index => $status) {
            $historyEntry = collect($history)->where('status', $status)->last();

            $statusTimeline[] = [
                'status' => $status,
                'completed' => $historyEntry !== null,
                'timestamp' => $historyEntry['changed_at'] ?? null,
                'changed_by' => $historyEntry['changed_by'] ?? null,
            ];
        }

        // Calculate estimated remaining time
        $estimatedRemaining = null;
        $estimatedFinishFormatted = null;
        $countdownTarget = null;

        if ($order->estimated_finish) {
            $finish = \Carbon\Carbon::parse($order->estimated_finish);
            $estimatedFinishFormatted = $finish->format('d M Y, H:i');
            $countdownTarget = $finish->toISOString();

            if (!in_array($order->status, ['Selesai', 'Diambil'])) {
                $estimatedRemaining = $order->estimated_remaining;
            }
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
            'estimated_finish' => $estimatedFinishFormatted,
            'countdown_target' => $countdownTarget,
            'estimated_remaining' => $estimatedRemaining,
            'progress_percentage' => $order->getProgressPercentage(),
            'status_timeline' => $statusTimeline,
            'status_updated_at' => $order->status_updated_at ? $order->status_updated_at->format('d M Y, H:i') : null,
            'photos' => $order->photos->map(fn($p) => [
                'url' => $p->photo_url,
                'caption' => $p->caption,
                'type' => $p->type,
                'type_label' => $p->type_label,
                'created_at' => $p->created_at->format('d M Y, H:i'),
            ])->groupBy('type'),
        ];
    }
}
