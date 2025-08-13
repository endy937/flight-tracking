@extends('layouts.app')

@section('title', 'Flight Tracking Admin')

@section('contents')
    <h2>Dashboard Pemantauan Udara Real-Time</h2>
    <div class="my-4 p-4 bg-blue-100 text-blue-900 rounded-lg shadow font-semibold text-lg bottom-10" id="laporan-5menit">
        Menunggu laporan data per 5 menit...
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="reportCards">
        <!-- Kartu laporan akan diisi lewat JavaScript -->
    </div>
    <div style="margin-top: 20px; overflow-x:auto;">
        <h3>Tabel Ringkasan Jumlah Pesawat per Waktu</h3>
        <table id="summaryTable" border="1" style="width:100%; border-collapse: collapse;">
            <thead style="background:#eee;">
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Jumlah Pesawat</th>
                </tr>
            </thead>
            <tbody id="summaryTableBody"></tbody>
        </table>
    </div>

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
