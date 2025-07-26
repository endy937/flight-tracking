<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FlightLog;
use RealRashid\SweetAlert\Facades\Alert;

class FlightLogViewController extends Controller
{
    public function show($id)
    {
        $alert = 'Delete Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($alert, $text);
        $log = FlightLog::findOrFail($id);
        $flightData = json_encode($log->data); // JSON dari kolom `data`

        return view('flightlogs.index', compact('log', 'flightData'));
    }
    public function destroy($id)
    {
        $data = FlightLog::findOrFail($id);
        $data->delete();

        Alert::success('Sukses', 'Data berhasil dihapus');
        return redirect()->route('logsave_index');
    }

}

