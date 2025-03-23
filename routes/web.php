<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/weather', [WeatherController::class, 'index']);
//Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/weather/current', [WeatherController::class, 'getCurrentWeather']);
Route::get('/api/weather', [WeatherController::class, 'getWeather']);

// Route::get('/', function () {
//     return view('welcome');
// });
//http://127.0.0.1:8000/weather/current?lat=22.218094&lon=91.857086