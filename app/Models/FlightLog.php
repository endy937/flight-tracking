<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightLog extends Model
{
    protected $fillable = ['log_id', 'tanggal', 'timestamp', 'data'];

    protected $casts = [
        'data' => 'array',
        'timestamp' => 'datetime',
    ];
}
