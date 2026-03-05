<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'order_id',
        'for_role',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Scope: unread notifications for a specific role
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('for_role', $role)->orWhereNull('for_role');
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Helper: Create a notification
     */
    public static function notify(string $type, string $title, string $message, array $options = []): self
    {
        $iconMap = [
            'order_new' => 'ph-fill ph-receipt',
            'order_status' => 'ph-fill ph-arrow-clockwise',
            'payment' => 'ph-fill ph-currency-circle-dollar',
            'stock_low' => 'ph-fill ph-warning',
            'system' => 'ph-fill ph-info',
        ];

        $colorMap = [
            'order_new' => 'blue',
            'order_status' => 'purple',
            'payment' => 'green',
            'stock_low' => 'orange',
            'system' => 'blue',
        ];

        return static::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $options['icon'] ?? ($iconMap[$type] ?? 'ph-fill ph-bell'),
            'color' => $options['color'] ?? ($colorMap[$type] ?? 'blue'),
            'link' => $options['link'] ?? null,
            'order_id' => $options['order_id'] ?? null,
            'for_role' => $options['for_role'] ?? null,
        ]);
    }
}
