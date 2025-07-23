<?php

namespace App\Http\Controllers;

use App\Models\AdsbData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SaveAdsbController extends Controller
{
    public function index(){
    $alert = 'Delete Data!';
    $text = "Are you sure you want to delete?";
    confirmDelete($alert, $text);

    // Hanya ambil 10 data per halaman
    $data = AdsbData::latest()->paginate(10);

    return view('adsb.index', [
        'data' => $data
    ]);
}


    public function store(Request $request){
        $data=$request->all();

    // Cek apakah ini update atau create
    if (!empty($request->id)) {
 
      $dataUpate = AdsbData::findOrFail($request->id);
      $dataUpate->update($data);
    } else {
      AdsbData::create($data);

    }

        Alert::success('Success', 'Data berhasil ' . (!empty($request->id) ? 'diupdate' : 'disimpan'));
        return redirect()->route('adsb_index');
    }

public function destroy($id)
{
    $data = AdsbData::findOrFail($id);
    $data->delete();

    Alert::success('Success', 'Data Berhasil Dihapus');
    return redirect()->route('adsb_index');

}
}
