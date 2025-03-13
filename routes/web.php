<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/weather', [WeatherController::class, 'index']);
Route::post('/weather', [WeatherController::class, 'getWeather']);
Route::get('/weather/current', [WeatherController::class, 'getCurrentWeather']);

// Route::get('/', function () {
//     return view('welcome');
// });
