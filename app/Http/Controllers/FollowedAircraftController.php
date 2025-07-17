<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowedAircraft;

class FollowedAircraftController extends Controller
{
    public function store(Request $request)
    {
        $aircraft = new FollowedAircraft();
        $aircraft->user_id = auth()->id() ?? 1; // Kalau login, pakai ID login
        $aircraft->callsign = $request->callsign;
        $aircraft->lat = $request->lat;
        $aircraft->lon = $request->lon;
        $aircraft->registration = $request->registration;
        $aircraft->icao24bit = $request->icao24bit;
        $aircraft->save();

        return response()->json(['message' => 'Success', 'data' => $aircraft]);
    }
}
