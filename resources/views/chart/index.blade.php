@extends('layouts.app') <!-- Ganti sesuai layout kamu -->

@section('title', 'Chart Follow Aircraft')
@section('content')
    <div class="container">
        <h2 class="text-xl font-bold mb-4">Jumlah Pesawat di-Follow per Hari</h2>
        <canvas id="followedChart"></canvas>
    </div>

@endsection
