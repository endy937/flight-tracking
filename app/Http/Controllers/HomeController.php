<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $userlogin =Auth::user();
        // dd($userlogin);
        return view('home');
    }

    public function adminHome()
    {
        return view('dashboard');
    }
}
