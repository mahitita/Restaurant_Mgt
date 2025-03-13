<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'reservation_id', 'payment_method', 'amount',
        'deposit_amount', 'deposit_refunded', 'paid_at', 'status'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'deposit_refunded' => 'boolean',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}

