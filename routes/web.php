<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaveAdsbController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('register', 'register')->name('register');
    Route::post('register', 'registerSave')->name('register.save');

    Route::get('/login', 'login')->name('login');
    Route::post('login', 'loginAction')->name('login.action');

    Route::get('logout', 'logout')->middleware('auth')->name('logout');
    Route::post('logout', 'logout')->middleware('auth')->name('logout');
});

//Normal Users Routes List
Route::middleware(['auth', 'user-access:user'])->group(function () {
    Route::get('/h', [HomeController::class, 'index'])->name('home');
});

//Admin Routes List
Route::middleware(['auth', 'user-access:admin'])->group(function () {
    Route::get('/admin/home', [HomeController::class, 'adminHome'])->name('admin/home');
    Route::get('/admin/profile', [AdminController::class, 'profilepage'])->name('admin/profile');

    Route::get('/admin/user', [UserController::class, 'index'])->name('user_index');
    Route::get('/admin/user/{id}', [UserController::class, 'edit'])->name('user_edit');
    Route::get('/admin/create', [UserController::class, 'create'])->name('user_create');
    Route::post('/admin/user', [UserController::class, 'store'])->name('user_store');
    Route::delete('/admin/user/{id}/delete', [UserController::class, 'destroy'])->name('user_delete');

    Route::get('/admin/adsb', [SaveAdsbController::class, 'index'])->name('adsb_index');
    Route::post('/admin/adsb', [SaveAdsbController::class, 'store'])->name('adsb_store');
    Route::delete('/admin/adsb/{id}/delete', [SaveAdsbController::class, 'destroy'])->name('adsb_delete');

});

Route::post('/follow-aircraft', [FlightController::class, 'store'])->name('follow-aircraft');
Route::get('/admin/follow', [FlightController::class, 'index'])->name('follow_index');
Route::delete('/admin/follow/{id}/delete', [FlightController::class, 'destroy'])->name('follow_delete');

