<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_id', 'type', 'qty', 'notes', 'user_name'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
