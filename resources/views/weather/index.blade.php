<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-300 to-blue-500 font-sans antialiased">
    <div class="container mx-auto p-4 md:px-10">
        <!-- Title -->
        <h1 class="text-4xl font-extrabold text-center text-white mb-8">Weather Dashboard</h1>
        
        <!-- Search Form -->
        <form action="/weather" method="POST" class="flex justify-center items-center space-x-4 mb-8 bg-white p-4 rounded-xl shadow-xl max-w-lg mx-auto">
            @csrf
            <input type="text" name="city" placeholder="Enter city" class="p-3 w-2/3 md:w-1/2 text-gray-700 rounded-l-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white p-3 rounded-r-md hover:bg-blue-700 transition duration-200">
                Get Weather
            </button>
        </form>

        <!-- Weather Info -->
        @if(isset($weather))
            <div class="bg-white p-6 rounded-xl shadow-xl max-w-lg mx-auto">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">{{ $weather['name'] }}</h2>
                <div class="flex justify-between text-lg text-gray-700 mb-4">
                    <span>Temperature: {{ $weather['main']['temp'] }}°C</span>
                    <span>Humidity: {{ $weather['main']['humidity'] }}%</span>
                </div>
                <div class="flex justify-between text-lg text-gray-700 mb-4">
                    <span>Wind Speed: {{ $weather['wind']['speed'] }} m/s</span>
                    <span>Weather: {{ $weather['weather'][0]['description'] }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- TailwindCSS Live Version for Debugging -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
</body>
</html>
