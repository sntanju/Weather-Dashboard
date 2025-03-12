<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Weather Dashboard</title>
   
    
    
</head>
<body class="bg-gradient-to-r from-blue-400 to-blue-700 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-3xl w-full">
        <!-- Title -->
        <h1 class="text-4xl font-extrabold text-center text-white mb-6">Weather Dashboard</h1>

        <!-- Search Form -->
        <form action="/weather" method="POST" class="flex flex-col md:flex-row justify-center items-center space-y-3 md:space-y-0 md:space-x-4 bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
            @csrf
            <input type="text" name="city" placeholder="Enter city name" class="p-3 w-full md:w-2/3 text-gray-700 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-md hover:bg-blue-700 transition duration-200">
                Get Weather
            </button>
        </form>

        <!-- Weather Info -->
        @if(isset($weather) && isset($weather['main']))
            <div class="mt-8 bg-white p-6 rounded-xl shadow-xl max-w-lg mx-auto">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">{{ $weather['name'] }}</h2>
                <div class="flex justify-center mb-4">
                    <img src="http://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png" alt="Weather Icon" class="w-20 h-20">
                </div>
                <p class="text-xl text-center text-gray-700">{{ ucfirst($weather['weather'][0]['description']) }}</p>

                <div class="grid grid-cols-2 gap-4 text-lg text-gray-700 mt-6">
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌡 Temperature</p>
                        <p>{{ $weather['main']['temp'] }}°C</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">💧 Humidity</p>
                        <p>{{ $weather['main']['humidity'] }}%</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌬 Wind Speed</p>
                        <p>{{ $weather['wind']['speed'] }} m/s</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌅 Sunrise</p>
                        <p>{{ date('h:i A', $weather['sys']['sunrise']) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

</body>
</html>
