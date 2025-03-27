<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role'];

    protected $hidden = ['password'];
    protected $primaryKey = 'id'; // Should be 'id' (default), not 'phone'
    public $incrementing = true;  // Should be true for auto-incrementing ID



    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function waitlists()
    {
        return $this->hasMany(Waitlist::class);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
}
