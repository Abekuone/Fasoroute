<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\RegisterController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Trip\TripController;
use App\Http\Controllers\Booking\BookingController;

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


Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/show', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::delete('/close', [ProfileController::class, 'closeAccount']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('trips')->group(function () {
        Route::post('/post-trips', [TripController::class, 'store']);
        Route::get('/show-trip/{id}', [TripController::class, 'show']);
        Route::get('/all-trips', [TripController::class, 'index']);
        Route::put('/update-trip/{id}', [TripController::class, 'update']);
        Route::delete('/delete-trip/{id}', [TripController::class, 'destroy']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('bookings')->group(function () {
        Route::post('/{tripId}/create', [BookingController::class, 'store']);
        Route::get('/list', [BookingController::class, 'index']);
        Route::get('/show/{id}', [BookingController::class, 'show']);
        Route::put('/update/{id}', [BookingController::class, 'update']);
        Route::delete('/delete/{id}', [BookingController::class, 'destroy']);
    });
});
