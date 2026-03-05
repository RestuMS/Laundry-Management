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
        'order_source',
        'customer_id',
        'customer_name',
        'customer_phone',
        'service_name',
        'weight',
        'package_detail',
        'estimated_finish',
        'status',
        'status_history',
        'status_updated_at',
        'total_price',
        'discount',
        'tax',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'status_history' => 'array',
        'estimated_finish' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    /**
     * All possible statuses in order
     */
    public const STATUSES = [
        'Menunggu Konfirmasi',
        'Diterima',
        'Dicuci',
        'Dikeringkan',
        'Disetrika',
        'Quality Control',
        'Selesai',
        'Diambil',
    ];

    /**
     * Statuses for normal workflow (excludes pending/rejected)
     */
    public const WORKFLOW_STATUSES = [
        'Diterima',
        'Dicuci',
        'Dikeringkan',
        'Disetrika',
        'Quality Control',
        'Selesai',
        'Diambil',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function photos()
    {
        return $this->hasMany(OrderPhoto::class);
    }

    /**
     * Get total amount paid for this order
     */
    public function getTotalPaidAttribute(): int
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the grand total (total_price - discount + tax)
     */
    public function getGrandTotalAttribute(): int
    {
        return $this->total_price - ($this->discount ?? 0) + ($this->tax ?? 0);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): int
    {
        return max(0, $this->grand_total - $this->total_paid);
    }

    /**
     * Record a status change into the status_history JSON
     */
    public function recordStatusChange(string $newStatus, ?string $changedBy = null): void
    {
        $history = $this->status_history ?? [];

        $history[] = [
            'status' => $newStatus,
            'changed_at' => now()->toISOString(),
            'changed_by' => $changedBy ?? (auth()->user()?->name ?? 'System'),
        ];

        $this->status_history = $history;
        $this->status_updated_at = now();
    }

    /**
     * Get the current status index (0-based)
     */
    public function getStatusIndex(): int
    {
        return array_search($this->status, self::STATUSES) ?: 0;
    }

    /**
     * Get the progress percentage (0-100)
     */
    public function getProgressPercentage(): int
    {
        $index = $this->getStatusIndex();
        $total = count(self::STATUSES) - 1;
        return $total > 0 ? round(($index / $total) * 100) : 0;
    }

    /**
     * Get estimated remaining time as human-readable string
     */
    public function getEstimatedRemainingAttribute(): ?string
    {
        if (!$this->estimated_finish || $this->status === 'Selesai' || $this->status === 'Diambil') {
            return null;
        }

        $now = now();
        $finish = $this->estimated_finish;

        if ($finish->isPast()) {
            return 'Segera selesai';
        }

        $diff = $now->diff($finish);

        if ($diff->days > 0) {
            return $diff->days . ' hari ' . $diff->h . ' jam lagi';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam ' . $diff->i . ' menit lagi';
        } else {
            return $diff->i . ' menit lagi';
        }
    }
}
