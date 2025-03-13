<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;

class WeatherController extends Controller
{
    public function index()
    {
        // Attempt to get weather for the user's location if available
        $weather = null;
        return view('weather.index', compact('weather'));
    }

    public function getWeather(Request $request)
    {
        $city = $request->city;
        $apiKey = env('WEATHER_API_KEY');
    
        // Fetch weather based on city or coordinates
        if ($city) {
            $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
        } else {
            // If no city, get the coordinates (latitude, longitude)
            $lat = $request->lat;
            $lon = $request->lon;
    
            $response = Http::get("http://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
        }
    
        // Check the structure of the response
        $weather = $response->json();
    
        // Debug log to see the response
        \Log::info('Weather API Response:', $weather);
    
        return view('weather.index', compact('weather'));
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
    
        $response = Http::withoutVerifying()->get($url); // Using the correctly formatted URL
    
        if ($response->successful()) {
            return response()->json($response->json()); // Return the data only if successful
        }
    
        // Log error if the request fails
        Log::error('Weather API Error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    
        return response()->json(['error' => 'Unable to fetch weather data'], $response->status());
    }
    
}
