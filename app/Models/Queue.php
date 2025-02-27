<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'group_size', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

