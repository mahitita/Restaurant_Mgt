<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'remaining',
        'unit_cost',
        'unit',
        'threshold',
        'expiry_date',
    ];

    protected $dates = ['expiry_date'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'inventory_menu')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function addStock($amount, $totalCost, $supplier = null)
    {
        // Store original quantity and unit cost before increment
        $originalQuantity = $this->quantity;
        $originalUnitCost = $this->unit_cost;

        // Increment quantity
        $this->increment('quantity', $amount);

        // Create purchase record
        $this->purchases()->create([
            'quantity' => $amount,
            'cost' => $totalCost,
            'supplier' => $supplier,
        ]);

        // Log the addition
        $this->logs()->create([
            'action' => 'added',
            'quantity' => $amount,
        ]);

        // Calculate new unit cost using weighted average
        $existingValue = $originalQuantity * $originalUnitCost;
        $newValue = $totalCost;
        $newTotalQuantity = $originalQuantity + $amount;
        $this->unit_cost = ($existingValue + $newValue) / $newTotalQuantity;

        $this->save();
    }

    public function deductStock($amount)
    {
        if ($this->quantity >= $amount) {
            $this->decrement('quantity', $amount);

            // Log the deduction
            $this->logs()->create([
                'action' => 'deducted',
                'quantity' => $amount,
            ]);
        } else {
            throw new \Exception("Insufficient stock for {$this->name}");
        }
    }

public function isLowStock()
    {
        $threshold = $this->threshold;
        return $this->remaining <= $threshold;
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->greaterThan($this->expiry_date);
    }

    // New method to get initial stock added
    public function getInitialStockAttribute()
    {
        return $this->logs()
            ->where('action', 'added')
            ->orderBy('created_at', 'asc')
            ->first()
            ->quantity ?? 0;
    }

    // New method to get total stock added over time
    public function getTotalStockAddedAttribute()
    {
        return $this->logs()
            ->where('action', 'added')
            ->sum('quantity');
    }
}