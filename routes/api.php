<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function() {
    Route::get('/weather/current', [WeatherController::class, 'current']);
    Route::get('/weather/location', [WeatherController::class, 'location']);
    Route::get('/weather/forecast', [WeatherController::class, 'forecast']);
});
