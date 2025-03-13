<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    public function isAvailable($dateTime)
    {
        $reservationDate = $dateTime->toDateString();
        $hasReservation = $this->reservations()
            ->where('status', 'confirmed')
            ->whereDate('reservation_time', $reservationDate)
            ->exists();

        Log::info("Checking availability for Table {$this->id} on {$reservationDate}: Status = {$this->status}, Has Reservation = " . ($hasReservation ? 'Yes' : 'No'));

        return !$hasReservation; // Only block if there's a confirmed reservation for this date
    }
    /**
     * Get the orders for the table.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
