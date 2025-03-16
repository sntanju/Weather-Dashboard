<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class WeatherController extends Controller
{
    public function index()
    {
        // Attempt to get weather for the user's location if available
        $lat = 23.8103;
        $lon = 90.4125;
        $weather = null;
        return view('weather.index', compact('weather', 'lat', 'lon'));
    }

    public function getWeather(Request $request)
    {
        $city = $request->input('city');
        $apiKey = env('WEATHER_API_KEY');
    
        if ($city) {
            // Fetch weather for searched city
            $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
        } else {
            // If no city, fallback to user coordinates
            $lat = $request->input('lat', 23.8103);
            $lon = $request->input('lon', 90.4125);

            $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
        }

        if ($response->failed()) {
            return response()->json(['error' => 'City not found or API failed'], 404);
        }

        $weather = $response->json();
        Log::info('Weather API Response:', $weather);

        // Ensure latitude & longitude are included
        $lat = $weather['coord']['lat'];
        $lon = $weather['coord']['lon'];

        return view('weather.index', compact('weather', 'lat', 'lon'));
    }

    public function getCurrentWeather(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Latitude and Longitude are required'], 400);
        }

        $apiKey = env('WEATHER_API_KEY'); 
        $url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$apiKey&units=metric";

        $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        Log::error('Weather API Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return response()->json(['error' => 'Unable to fetch weather data'], $response->status());
    }
    
}
