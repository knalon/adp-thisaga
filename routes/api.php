<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\AppointmentController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Car API Routes
Route::get('/cars/models', [CarController::class, 'getModelsByMake']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cars/{car}/appointments', [AppointmentController::class, 'store']);
    Route::post('/cars/{car}/bids', [AppointmentController::class, 'updateBid']);
    Route::get('/cars/{car}/highest-bid', [AppointmentController::class, 'getHighestBid']);
    Route::get('/user/appointments', [AppointmentController::class, 'getUserAppointments']);
    Route::post('/cars/{car}/appointments/cancel', [AppointmentController::class, 'cancel']);
});
