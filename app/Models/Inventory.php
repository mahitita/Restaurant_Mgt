<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'threshold', 'expiry_date'];


    public function purchases() {
        return $this->hasMany(Purchase::class);
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function addStock($amount)
    {
        $this->increment('quantity', $amount);
        $this->logs()->create(['action' => 'added', 'quantity' => $amount]);
    }

    public function deductStock($amount)
    {
        $this->decrement('quantity', $amount);
        $this->logs()->create(['action' => 'deducted', 'quantity' => $amount]);
    }



}
