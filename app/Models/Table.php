<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Table extends Model
{
    use HasFactory;

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

    protected $attributes = [
        'status' => 'available',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAvailable($dateTime)
    {
        $reservationDate = Carbon::parse($dateTime)->toDateString();
        return !$this->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', $reservationDate)
            ->exists();
    }

    public function getStatusForDate($dateTime = null)
    {
        $date = Carbon::parse($dateTime ?? now())->toDateString();
        $reservation = $this->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', $date)
            ->first();

        if ($reservation) {
            return 'reserved'; // Reserved for this day
        }

        // If no reservation, return the base status (could be occupied or available)
        return $this->status;
    }
}