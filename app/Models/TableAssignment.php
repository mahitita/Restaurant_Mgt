<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'reservation_id', 'queue_id', 'assigned_at'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }
}

