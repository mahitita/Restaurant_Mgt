<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'remaining_quantity',
        'unit_cost',
        'unit',
        'threshold',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'string',
        'remaining_quantity' => 'string',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function isLowStock()
    {
        return (int) $this->remaining_quantity <= $this->threshold;
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->greaterThan($this->expiry_date);
    }
}