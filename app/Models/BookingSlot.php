<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class BookingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_date',
        'max_capacity_kg',
        'booked_kg',
        'is_open',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'max_capacity_kg' => 'float',
        'booked_kg' => 'float',
        'is_open' => 'boolean',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ──────────────────────────────────────────────
    // Computed Attributes
    // ──────────────────────────────────────────────

    /**
     * Remaining capacity in kg
     */
    public function getRemainingKgAttribute(): float
    {
        return max(0, $this->max_capacity_kg - $this->booked_kg);
    }

    /**
     * Percentage of capacity used
     */
    public function getUsagePercentageAttribute(): int
    {
        if ($this->max_capacity_kg <= 0) return 100;
        return min(100, (int) round(($this->booked_kg / $this->max_capacity_kg) * 100));
    }

    /**
     * Whether slot is fully booked
     */
    public function getIsFullAttribute(): bool
    {
        return $this->booked_kg >= $this->max_capacity_kg;
    }

    /**
     * Whether this slot is available (open + not full + in the future)
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->is_open && !$this->is_full && $this->booking_date->isFuture();
    }

    // ──────────────────────────────────────────────
    // Static Helpers
    // ──────────────────────────────────────────────

    /**
     * Get or create a slot for the given date
     * Uses the default capacity from Settings
     */
    public static function getOrCreateForDate(string $date): self
    {
        $defaultCapacity = (float) (Setting::getValue('daily_capacity_kg', 50));

        return self::firstOrCreate(
            ['booking_date' => $date],
            [
                'max_capacity_kg' => $defaultCapacity,
                'booked_kg' => 0,
                'is_open' => true,
            ]
        );
    }

    /**
     * Get available slots for the next N days (for customer booking)
     */
    public static function getAvailableSlots(int $days = 14): \Illuminate\Support\Collection
    {
        $defaultCapacity = (float) (Setting::getValue('daily_capacity_kg', 50));
        $slots = collect();

        for ($i = 1; $i <= $days; $i++) {
            $date = Carbon::today()->addDays($i);
            $dateStr = $date->toDateString();

            // Skip Sundays (closed day) — configurable if needed
            // Uncomment below to close Sundays automatically
            // if ($date->isSunday()) continue;

            $slot = self::firstOrCreate(
                ['booking_date' => $dateStr],
                [
                    'max_capacity_kg' => $defaultCapacity,
                    'booked_kg' => 0,
                    'is_open' => true,
                ]
            );

            $slots->push($slot);
        }

        return $slots;
    }

    /**
     * Reserve capacity for an order
     * Returns false if slot is full
     */
    public static function reserveCapacity(string $date, float $kg): ?self
    {
        $slot = self::getOrCreateForDate($date);

        if (!$slot->is_open) return null;
        if ($slot->booked_kg + $kg > $slot->max_capacity_kg) return null;

        $slot->increment('booked_kg', $kg);
        $slot->refresh();

        return $slot;
    }

    /**
     * Release capacity when order is cancelled
     */
    public static function releaseCapacity(int $slotId, float $kg): void
    {
        $slot = self::find($slotId);
        if ($slot) {
            $slot->booked_kg = max(0, $slot->booked_kg - $kg);
            $slot->save();
        }
    }
}
