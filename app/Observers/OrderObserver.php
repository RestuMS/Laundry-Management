<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Models\InAppNotification;
use App\Services\WhatsappNotificationService;

class OrderObserver
{
    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Record the status change into history
            $order->recordStatusChange($newStatus);

            // Activity Log
            ActivityLog::log(
                'updated',
                "Status order {$order->order_code} diubah: {$oldStatus} → {$newStatus}",
                $order,
                ['status' => $oldStatus],
                ['status' => $newStatus]
            );

            // In-app notification
            InAppNotification::notify(
                'order_status',
                'Status Order Berubah',
                "Order {$order->order_code} ({$order->customer_name}): {$oldStatus} → {$newStatus}",
                [
                    'order_id' => $order->id,
                    'link' => route('order.index'),
                ]
            );

            // Check if auto-notification is enabled
            $autoNotify = Setting::where('key', 'auto_wa_notification')->value('value');

            if ($autoNotify === '1' || $autoNotify === null) {
                $waService = app(WhatsappNotificationService::class);
                $waService->sendTrackingUpdate($order, $oldStatus);
            }
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Record initial status in history
        $history = [[
            'status' => $order->status,
            'changed_at' => now()->toISOString(),
            'changed_by' => auth()->user()?->name ?? 'System',
        ]];

        $order->status_history = $history;
        $order->status_updated_at = now();
        $order->saveQuietly();

        // Activity Log
        ActivityLog::log('created', "Order baru {$order->order_code} dibuat untuk {$order->customer_name} (Rp " . number_format($order->total_price, 0, ',', '.') . ")", $order);

        // In-app notification
        InAppNotification::notify(
            'order_new',
            'Order Baru Masuk',
            "Order {$order->order_code} dari {$order->customer_name} senilai Rp " . number_format($order->total_price, 0, ',', '.'),
            [
                'order_id' => $order->id,
                'link' => route('order.index'),
            ]
        );

        // Check if auto-notification is enabled for new orders
        $autoNotify = Setting::where('key', 'auto_wa_notification')->value('value');

        if ($autoNotify === '1' || $autoNotify === null) {
            $waService = app(WhatsappNotificationService::class);
            $waService->sendTrackingUpdate($order);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        ActivityLog::log('deleted', "Order {$order->order_code} ({$order->customer_name}) dihapus", $order);
    }
}
