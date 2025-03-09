<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'table_number',
        'seats',
        'type',
        'x_coordinate',
        'y_coordinate',
        'width',
        'height',
        'status',
    ];

    /**
     * The default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'available',
    ];

    /**
     * Get the reservations for the table.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the orders for the table.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
