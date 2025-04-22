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
    try {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $city = $request->query('city');

        $apiKey = env('WEATHER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API key is missing'], 500);
        }

        // If lat and lon are not provided, fall back to city search
        if ($lat && $lon) {
            $url = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$apiKey&units=metric";
        } elseif ($city) {
            $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";
        } else {
            return response()->json(['error' => 'City or Geolocation data is required'], 400);
        }

        Log::info("Fetching weather from URL: $url");

        //$response = Http::get($url);
        $response = Http::withoutVerifying()->get($url);


        if ($response->failed()) {
            Log::error('Weather API Response Error:', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json([
                'error' => 'Failed to fetch weather data',
                'details' => $response->json(),
            ], $response->status());
        }

        return response()->json($response->json());
    } catch (\Exception $e) {
        Log::error('Weather API Error: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error', 'details' => $e->getMessage()], 500);
    }
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
