<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowedAircraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'callsign',
        'lat',
        'lon',
        'registration',
        'icao24bit',
    ];
}


