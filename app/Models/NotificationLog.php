<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'channel',
        'phone',
        'type',
        'status_trigger',
        'message',
        'delivery_status',
        'response',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
