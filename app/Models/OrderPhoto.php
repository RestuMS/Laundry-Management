<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'photo_path',
        'caption',
        'type',
        'uploaded_by',
    ];

    /**
     * Possible photo types
     */
    public const TYPES = ['masuk', 'proses', 'selesai'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the full URL of the photo
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }

    /**
     * Get the type label in Indonesian
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'masuk' => 'Saat Masuk',
            'proses' => 'Saat Proses',
            'selesai' => 'Saat Selesai',
            default => $this->type,
        };
    }
}
