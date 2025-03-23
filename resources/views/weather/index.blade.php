<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Weather Dashboard</title>

    <style>
    .loader {
        border-width: 8px;
        border-radius: 50%;
        border-color: transparent;
        border-top-color:rgba(52, 111, 220, 0.55);
        border-right-color: rgba(52, 206, 220, 0.57); 
        border-bottom-color: rgba(52, 220, 74, 0.64); 
        border-left-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

</head>

<body class="bg-gradient-to-r from-blue-400 to-blue-700 font-sans antialiased min-h-screen flex flex-col items-center p-4">
    
         <!-- Title & Search Form -->
        <div class="w-full max-w-4xl text-center">
            <h1 class="text-4xl font-extrabold text-white mb-6">Weather Dashboard</h1>
            {{-- 
            <form action="/weather" method="POST" class="flex flex-col md:flex-row justify-center items-center space-y-3 md:space-y-0 md:space-x-4 bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
                @csrf
                <input type="text" name="city" id = "city" placeholder="Enter city name" class="p-3 w-full md:w-2/3 text-gray-700 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-md hover:bg-blue-700 transition duration-200">
                    Get Weather
                </button>
            </form>
            --}}

            <form id="weatherForm" class="flex flex-col md:flex-row justify-center items-center space-y-3 md:space-y-0 md:space-x-4 bg-white p-6 rounded-xl shadow-md max-w-lg mx-auto">
           
            <input type="text" name="city" id="city" placeholder="Enter city name" class="p-3 w-full md:w-2/3 text-gray-700 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-md hover:bg-blue-700 transition duration-200">
                Get Weather
            </button>
        </form>
        </div>

        <!-- Main Content: Windy Map & Weather Result -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-7xl"> 
    
            <!-- Weather Result (left Side) -->
            <div id="weather-container" class="bg-white  rounded-xl shadow-md">
                <div id="loader" class="flex justify-center items-center w-full h-[650px]">
                    <div class="loader animate-spin rounded-full border-t-4 border-blue-500 border-solid w-15 h-15"> </div>
                    
                </div>
            </div>

            <!-- Windy.com iframe (Right Side) -->
            <div class="bg-white p-2 rounded-xl shadow-md">
                <iframe id="windyFrame" class="w-full h-[650px]"
                    src="https://embed.windy.com/embed2.html?lat={{ $lat }}&lon={{ $lon }}&zoom=5&level=surface&overlay=wind&menu=true&message=true&marker=true&calendar=now&pressure=true&type=map&location=coordinates&detail=&metricWind=default&metricTemp=default&radarRange=-1"
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </div>

    <!-- JavaScript to Get User's Location -->
    <script>
       document.addEventListener("DOMContentLoaded", function () {
    console.log('Script loaded');
    let userSearched = false; // Flag to check if a search has been made

    // Form submission handler
    document.getElementById('weatherForm').addEventListener('submit', function (event) {
        event.preventDefault();  // Prevent form submission
        userSearched = true;     // Mark that user searched
        fetchWeather();          // Fetch weather data for searched city
    });

    // Check for geolocation permission
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                if (!userSearched) { // Only fetch location if user hasn't searched
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    fetchWeatherByLocation(lat, lon);
                }
            },
            function (error) {
                console.error("Geolocation error:", error);
            }
        );
    } else {
        console.error("Geolocation is not supported by this browser.");
    }

    // Fetch weather for the current location (only if no search has been done)
    function fetchWeatherByLocation(lat, lon) {
        if (userSearched) return; // Prevent overriding user search

        fetch(`/weather/current?lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(data => {
                if (data.name) {
                    updateWeatherUI(data, false); // Mark as geolocation data
                    console.log("Current location weather loaded");
                }
            })
            .catch(error => console.error('Error fetching weather data:', error.message));
    }

    // Fetch weather based on user input
    function fetchWeather() {
    let city = document.getElementById('city').value.trim();
    if (!city) {
        console.error("City input is empty");
        return;
    }

    console.log(`Fetching weather for: ${city}`);
    document.getElementById('loader');

    // Use city search directly instead of relying on geolocation
    fetch(`/api/weather?city=${city}`)  // Make sure your backend accepts city query
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("API Response:", data);

            if (data.error) {
                alert('City not found');
                return;
            }

            userSearched = true; // Ensure flag is set
            updateWeatherUI(data, true); // Pass `true` for user search
            updateWindy(data.coord.lat, data.coord.lon);
        })
        .catch(error => console.error('Error fetching weather:', error));
}


    // Update UI without overriding user search
    function updateWeatherUI(weather, isSearch = false) {
        console.log("Updating UI with data:", weather);

        const weatherContainer = document.getElementById('weather-container');
        weatherContainer.innerHTML = ''; 

        if (isSearch) {
            userSearched = true; // Confirm user searched
        }

        weatherContainer.innerHTML = `
            <div class="bg-white p-3 rounded-xl shadow-xl">
                <p class="text-center text-gray-800 mb-4 font-semibold">${isSearch ? 'Weather of Your Searched Location' : 'Weather of Your Current Location'}</p>
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">${weather.name}, ${weather.sys.country}</h2>
                <div class="flex justify-center mb-4">
                    <img src="http://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png" alt="Weather Icon" class="w-20 h-20">
                </div>
                <p class="text-xl text-center text-gray-700">${weather.weather[0].description}</p>
                <div class="grid grid-cols-3 gap-4 text-lg text-gray-700 mt-6">
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌡 Temperature</p>
                        <p>${weather.main.temp}°C / ${((weather.main.temp * 9/5) + 32).toFixed(1)}°F</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌡 Feels Like</p>
                        <p>${weather.main.feels_like}°C / ${((weather.main.feels_like * 9/5) + 32).toFixed(1)}°F</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">💧 Humidity</p>
                        <p>${weather.main.humidity}% / Dew Point: ${weather.main.dew_point ?? 'N/A'}°C</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌬 Wind Speed</p>
                        <p>${weather.wind.speed} m/s / ${(weather.wind.speed * 3.6).toFixed(1)} km/h</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌅 Sunrise & Sunset</p>
                        <p>${new Date(weather.sys.sunrise * 1000).toLocaleTimeString()} / ${new Date(weather.sys.sunset * 1000).toLocaleTimeString()}</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌊 Sea Level</p>
                        <p>${weather.main.sea_level ?? 'N/A'} hPa / ${weather.main.pressure} hPa</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">☁ Cloudiness</p>
                        <p>${weather.clouds.all}% / Visibility: ${(weather.visibility / 1000).toFixed(1)} km</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                       <p class="font-semibold">🌧 Precipitation (Last 1hr)</p>
                       <p>${weather.rain?.["1h"] ?? weather.snow?.["1h"] ?? '0'} mm</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center">
                        <p class="font-semibold">🌬 Wind Gust</p>
                        <p>${weather.wind.gust ? weather.wind.gust + ' m/s' : 'N/A'} / Direction: ${weather.wind.deg}°</p>
                    </div>
                </div>
            </div>
        `;
    }

    function updateWindy(lat, lon) {
         document.getElementById('windyFrame').src = `https://embed.windy.com/embed2.html?lat=${lat}&lon=${lon}&zoom=5&level=surface&overlay=wind&menu=true&message=true&marker=true&calendar=now&pressure=true&type=map&location=coordinates&detail=&metricWind=default&metricTemp=default&radarRange=-1`;
     }
});



    </script>

</body>
</html>
