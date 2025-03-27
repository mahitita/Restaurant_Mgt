<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category_id',
        'description',
        'image',
        'prep_time',
        'cost',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_menu')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }

    public function calculateCost()
    {
        return $this->inventories->sum(function ($inventory) {
            return $inventory->unit_cost * $inventory->pivot->quantity;
        });
    }

    public function isLowStock()
    {
        return $this->inventories->contains(function ($inventory) {
            return $inventory->quantity <= $inventory->threshold;
        });
    }
}