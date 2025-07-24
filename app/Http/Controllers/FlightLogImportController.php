<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\FlightLog;
use RealRashid\SweetAlert\Facades\Alert;

class FlightLogImportController extends Controller
{
    public function import($filename)
    {
        $path = base_path("flightradar-node/Database_pesawat/{$filename}");

        if (!file_exists($path)) {
            return response()->json(['error' => "File tidak ditemukan di $path"], 404);
        }

        $content = file_get_contents($path);
        $logs = json_decode($content, true);

        if (!is_array($logs)) {
            return response()->json(['error' => "Format JSON tidak valid."], 400);
        }

        foreach ($logs as $log) {
            FlightLog::updateOrCreate(
                ['log_id' => $log['id']],
                [
                    'tanggal' => $log['tanggal'],
                    'timestamp' => $log['timestamp'],
                    'data' => json_encode($log['data']), // simpan sebagai string JSON
                ]
            );
        }

        return response()->json(['message' => "Import dari {$filename} berhasil"]);
    }

    public function index()
    {
        $data = FlightLog::latest()->paginate(5); // gunakan pagination

        return view('logsave.index', compact('data'));
    }

    public function destroy($id)
    {
        $data = FlightLog::findOrFail($id);
        $data->delete();

        Alert::success('Sukses', 'Data berhasil dihapus');
        return redirect()->route('logsave_index');
    }
}
