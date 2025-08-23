<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use function Ramsey\Uuid\v1;

class UserController extends Controller
{
    public function index(){
        $alert = 'Delete Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($alert, $text);
        $data = User::latest()->get();

        // dd($data);

        return view('user/index',[
            'data'=>$data
        ]);
    }

    public function create(){
        return view('user/form');
    }
    public function store(Request $request){
        $data=$request->all();
        $userLogin = Auth::user()->name;

    // Cek apakah ini update atau create
    if (!empty($request->id)) {
 
      $dataUpate = User::findOrFail($request->id);
      $dataUpate ['created_by']=$userLogin;
      $dataUpate->update($data);
    } else {
        $data['created_by']=$userLogin;
      User::create($data);

    }

        Alert::success('Success', 'Data berhasil ' . (!empty($request->id) ? 'diupdate' : 'disimpan'));
        return redirect()->route('user_index');
    }

 public function edit($id)
{
    $data = User::findOrFail($id);
    return view('user.form', [
        'data'=>$data
    ]);
}
public function destroy($id)
{
    $data = User::findOrFail($id);
    $data->delete();

    Alert::success('Success', 'Data Berhasil Dihapus');
    return redirect()->route('user_index');

}


}
