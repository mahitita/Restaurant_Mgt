<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'table_id', 'reservation_time', 'status'];
    protected $casts = [
        'reservation_time' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();;
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

