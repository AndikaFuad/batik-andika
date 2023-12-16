<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
route::get('pesan/{id}', [PesanController::class,'index']);
Route::post('pesan/{id}', [PesanController::class,'pesan']);
Route::get('check_out', [PesanController::class,'check_out']);
Route::delete('check-out/{id}', [PesanController::class,'delete']);

Route::get('konfirmasi-check-out', [PesanController::class,'konfirmasi']);
Route::get('profile', [ProfileController::class, 'index']);
Route::post('profile', [ProfileController::class, 'update']);

Route::get('history', [HistoryController::class, 'index']);
Route::get('history/{id}', [HistoryController::class, 'detail']);

Route::post('payments/midtrans-notification', [PaymentCallbackController::class, 'receive']);

Route::get('midtrans',[MidtransController::class, 'index']);