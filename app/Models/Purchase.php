<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'quantity',
        'cost',
        'supplier',
        'purchased_at',
    ];

    protected $dates = ['purchased_at'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}