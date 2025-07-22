<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowAircraft;
use Illuminate\Support\Facades\Auth;

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
        $userLogin = Auth::user()->name;

        $data['created_by']=$userLogin;

        FollowAircraft::create($data);

        return response()->json($data);
    }
}
