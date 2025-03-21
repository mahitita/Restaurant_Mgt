<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'user_id', 'party_size', 'added_at', 'estimated_wait_minutes', 
        'status', 'table_id', 'notified_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}