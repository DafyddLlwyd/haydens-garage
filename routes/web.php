<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BookingController;

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

// web middleware group
Route::middleware('web')->group(function () {
    Route::get('/', [IndexController::class, 'show'])->name('index');
    Route::get('/admin', [BookingController::class, 'show'])->name('admin');
});
