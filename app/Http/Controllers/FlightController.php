<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowAircraft;

class FlightController extends Controller
{
    public function storeFollowAircraft(Request $request)
    {
        // Validasi basic
        $validated = $request->validate([
            'callsign' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'registration' => 'nullable|string',
            'icao24bit' => 'nullable|string',
        ]);

        // Simpan
        $follow = FollowAircraft::create($validated);

        return response()->json([
            'message' => '✅ Data pesawat berhasil disimpan!',
            'data' => $follow,
        ]);
    }
    public function store (Request $request){
        $data = $request->all();

         FollowAircraft::create($data);

        return response()->json($data);
    }
}
