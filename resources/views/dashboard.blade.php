@extends('layouts.app')

@section('title', 'Flight Tracking Admin')

@section('contents')
    <h2>Dashboard Pemantauan Udara Real-Time</h2>
    <div class="container mx-auto py-4 px-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="aircraftCountChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="avgAltitudeChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="avgSpeedChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="airlineChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="headingDistributionChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="altitudeDistributionChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="regionChart" class="w-full h-full" height="160"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow p-4 h-50 flex items-center justify-center">
                <canvas id="speedExtremesChart" class="w-full h-full" height="160"></canvas>
            </div>
        </div>
    </div>



@endsection
