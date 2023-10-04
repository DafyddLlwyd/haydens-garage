<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BookingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('web')->group(function () {
    // store a new booking
    Route::post('/book', [IndexController::class, 'book'])->name('book');

    //store a new locked date
    Route::post('/lock', [BookingController::class, 'lock'])->name('lock');

    // delete a locked date
    Route::post('/unlock', [BookingController::class, 'unlock'])->name('unlock');
});
