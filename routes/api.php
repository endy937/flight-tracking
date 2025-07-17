<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdsbController;
use App\Http\Controllers\FollowedAircraftController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\FlightController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/save-adsb', [AdsbController::class, 'store']);
// Route::middleware('auth')->group(function () {
//     Route::post('/follow-aircraft', [FollowedAircraftController::class, 'store']);
//     Route::get('/follow-aircraft', [FollowedAircraftController::class, 'index']);
// });

// Route::post('/follow-aircraft', [AircraftController::class, 'storeFollowedAircraft']);



// Route::post('/follow-aircraft', [FlightController::class, 'storeFollowAircraft']);
