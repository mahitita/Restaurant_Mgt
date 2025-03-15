<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'threshold', 'expiry_date'];

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->threshold;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->isToday() || $this->expiry_date->isPast();
    }

    public function menus()
{
    return $this->belongsToMany(Menu::class, 'menu_ingredient')->withPivot('quantity');
}

}
