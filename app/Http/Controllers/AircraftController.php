<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AircraftController extends Controller
{
    public function storeFollowedAircraft(Request $request)
{
    $validated = $request->validate([
        'callsign' => 'required|string',
        'lat' => 'required|numeric',
        'lon' => 'required|numeric',
        'registration' => 'nullable|string',
        'icao24bit' => 'nullable|string',
    ]);

    // Simpan ke tabel followed_aircrafts
    $followed = new \App\Models\FollowedAircraft();
    $followed->callsign = $validated['callsign'];
    $followed->latitude = $validated['lat'];
    $followed->longitude = $validated['lon'];
    $followed->registration = $validated['registration'] ?? '-';
    $followed->icao24bit = $validated['icao24bit'] ?? '-';
    $followed->user_id = auth()->id(); // ✅ kalau pakai auth komandan
    $followed->save();

    return response()->json(['message' => 'Aircraft followed saved successfully']);
}

}
