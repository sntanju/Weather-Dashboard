<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Weather Dashboard</title>
</head>
<body class="bg-gradient-to-r from-blue-400 to-blue-700 font-sans antialiased min-h-screen flex flex-col items-center justify-center p-4">
    
   
    <div class="container mx-auto text-center">
        
        <!-- Title -->
        <h1 class="text-4xl font-extrabold text-center text-white mb-6">Weather Dashboard</h1>

        <input type="hidden" id="lat" value="{{ $lat }}">
        <input type="hidden" id="lon" value="{{ $lon }}">

        <!-- Windy.com iframe -->
        <div class="mt-4">
            <iframe id="windyFrame" width="100%" height="500"
                src="https://embed.windy.com/embed2.html?lat={{ $lat }}&lon={{ $lon }}&zoom=5&level=surface&overlay=wind&menu=true&message=true&marker=true&calendar=now&pressure=true&type=map&location=coordinates&detail=&metricWind=default&metricTemp=default&radarRange=-1"
                frameborder="0">
            </iframe>
        </div>
    </div>

    <div class="max-w-4xl w-full">

        <!-- Search Form -->
        <form action="/weather" method="POST" class="flex flex-col md:flex-row justify-center items-center space-y-3 md:space-y-0 md:space-x-4 bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
            @csrf
            <input type="text" name="city" placeholder="Enter city name" class="p-3 w-full md:w-2/3 text-gray-700 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-md hover:bg-blue-700 transition duration-200">
                Get Weather
            </button>
        </form>

        <!-- Weather Results -->
        <div id="weather-container" class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Searched Location Weather -->
            @if(isset($weather) && isset($weather['name']))
                <div class="bg-white p-6 rounded-xl shadow-xl">
                    <p class="text-center text-gray-800 mb-4 font-semibold">Weather Of Your Searched Location</p>
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">{{ $weather['name'] }}</h2>
                    <div class="flex justify-center mb-4">
                        <img src="http://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@2x.png" alt="Weather Icon" class="w-20 h-20">
                    </div>
                    <p class="text-xl text-center text-gray-700">{{ ucfirst($weather['weather'][0]['description']) }}</p>

                    <div class="grid grid-cols-3 gap-4 text-lg text-gray-700 mt-6">
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌡 Temperature</p>
                            <p>{{ $weather['main']['temp'] }}°C</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌡 Feels Like</p>
                            <p>{{ $weather['main']['feels_like'] }}°C</p>
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
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌊 Sea Level</p>
                            <p>{{ $weather['main']['sea_level'] ?? 'N/A' }} hPa</p>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript to Get User's Location -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        fetchWeatherByLocation(lat, lon);
                    },
                    function(error) {
                        console.error("Geolocation error:", error);
                    }
                );
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        });

        function fetchWeatherByLocation(lat, lon) {
            fetch(`/weather/current?lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    if (data.name) {
                        updateWeatherUI(data);
                    }
                })
                .catch(error => console.error('Error fetching weather data:', error.message));
        }

        function updateWeatherUI(weather) {
            const weatherContainer = document.getElementById('weather-container');
            weatherContainer.innerHTML += `
                <div class="bg-white p-6 rounded-xl shadow-xl">
                    <p class="text-center text-gray-800 mb-4 font-semibold">Weather Of Your Current Location</p>
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">${weather.name}</h2>
                    <div class="flex justify-center mb-4">
                        <img src="http://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png" alt="Weather Icon" class="w-20 h-20">
                    </div>
                    <p class="text-xl text-center text-gray-700">${weather.weather[0].description}</p>
                    <div class="grid grid-cols-3 gap-4 text-lg text-gray-700 mt-6">
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌡 Temperature</p>
                            <p>${weather.main.temp}°C</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌡 Feels Like</p>
                            <p>${weather.main.feels_like}°C</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">💧 Humidity</p>
                            <p>${weather.main.humidity}%</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌬 Wind Speed</p>
                            <p>${weather.wind.speed} m/s</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌅 Sunrise</p>
                            <p>${new Date(weather.sys.sunrise * 1000).toLocaleTimeString()}</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <p class="font-semibold">🌊 Sea Level</p>
                            <p>${weather.main.sea_level ?? 'N/A' } hPa</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function updateWindy(lat, lon) {
        document.getElementById('windyFrame').src = `https://embed.windy.com/embed2.html?lat=${lat}&lon=${lon}&zoom=5&level=surface&overlay=wind&menu=true&message=true&marker=true&calendar=now&pressure=true&type=map&location=coordinates&detail=&metricWind=default&metricTemp=default&radarRange=-1`;
    }

    function fetchWeather() {
        let city = document.getElementById('city').value;

        fetch(`/api/weather?city=${city}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('City not found');
                    return;
                }
                document.getElementById('lat').value = data.lat;
                document.getElementById('lon').value = data.lon;
                updateWindy(data.lat, data.lon);
            })
            .catch(error => console.error('Error:', error));
    
    
    }
    </script>

</body>
</html>
