<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function() {
    Route::get('/weather', [WeatherController::class, 'current']);
    Route::get('/weather/search', [WeatherController::class, 'search']);
    Route::get('/weather/forecast', [WeatherController::class, 'forecast']);
});
