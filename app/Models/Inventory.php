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

    public function addStock($amount, $totalCost, $supplier = null)
    {
        $this->increment('quantity', $amount);

        // Create purchase record
        $purchase = $this->purchases()->create([
            'quantity' => $amount,
            'cost' => $totalCost,
            'supplier' => $supplier,
        ]);

        // Update unit_cost as weighted average
        $totalQuantity = $this->quantity;
        $existingValue = ($this->quantity - $amount) * $this->unit_cost;
        $newValue = $totalCost;
        $this->unit_cost = ($existingValue + $newValue) / $totalQuantity;
        $this->save();

        return $purchase;
    }

    public function deductStock($amount)
    {
        if ($this->quantity >= $amount) {
            $this->decrement('quantity', $amount);
        } else {
            throw new \Exception("Insufficient stock for {$this->name}");
        }
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->threshold;
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->greaterThan($this->expiry_date);
    }
}