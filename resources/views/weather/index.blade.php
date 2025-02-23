<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-blue-100 font-sans antialiased">
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center text-gray-700 mb-6">Weather Dashboard</h1>
        <form action="/weather" method="POST" class="mb-6 flex justify-center">
            @csrf
            <input type="text" name="city" placeholder="Enter city" class="p-2 rounded-l-md border border-gray-300">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-r-md">Get Weather</button>
        </form>

        @if(isset($weather))
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-center mb-4">{{ $weather['name'] }}</h2>
                <div class="flex justify-between mb-4">
                    <span class="text-xl">Temperature: {{ $weather['main']['temp'] }}°C</span>
                    <span class="text-xl">Humidity: {{ $weather['main']['humidity'] }}%</span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="text-xl">Wind Speed: {{ $weather['wind']['speed'] }} m/s</span>
                    <span class="text-xl">Weather: {{ $weather['weather'][0]['description'] }}</span>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
