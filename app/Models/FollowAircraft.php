<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowAircraft extends Model
{
    use HasFactory;

    protected $table = 'follow_aircraft';

    protected $fillable = [
        'created_by',
        'callsign',
        'lat',
        'lon',
        'registration',
        'icao24bit',
    ];
}
