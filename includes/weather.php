<?php
function getWeather($city = 'Tirana', $country = 'AL')
{
    // WeatherAPI.com - sign up for free at https://www.weatherapi.com/
    $apiKey = '03d7cdb7226d4348aec102500251605';
    $url = "https://api.weatherapi.com/v1/current.json?key={$apiKey}&q={$city}&aqi=no";

    // Fallback data for Tirana
    $default = [
        'temp' => 24,
        'condition' => 'Sunny',
        'icon' => 'sun'
    ];

    // Check for cached data
    $cacheFile = '../cache/weather.json';
    $cacheTime = 3600; // 1 hour cache

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        if ($data)
            return $data;
    }

    // Use file_get_contents instead of cURL
    try {
        // Create stream context with timeout
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        // Suppress warnings with @ operator
        $response = @file_get_contents($url, false, $context);

        if ($response !== FALSE) {
            $data = json_decode($response, true);
            if (isset($data['current']['temp_c'])) {
                $result = [
                    'temp' => round($data['current']['temp_c']),
                    'condition' => $data['current']['condition']['text'],
                    'icon' => getWeatherIcon($data['current']['condition']['text'])
                ];

                // Create cache directory if needed
                if (!file_exists('../cache')) {
                    @mkdir('../cache', 0755, true);
                }

                // Cache the result
                @file_put_contents($cacheFile, json_encode($result));

                return $result;
            }
        }
    } catch (Exception $e) {
        // Do nothing, fall through to default
    }

    // If we reached here, something failed - use default data
    return $default;
}

// Helper function to map weather conditions to Font Awesome icons
function getWeatherIcon($condition)
{
    $condition = strtolower($condition);

    if (strpos($condition, 'sun') !== false || strpos($condition, 'clear') !== false) {
        return 'sun';
    } elseif (strpos($condition, 'cloud') !== false || strpos($condition, 'overcast') !== false) {
        return 'cloud';
    } elseif (strpos($condition, 'rain') !== false || strpos($condition, 'drizzle') !== false) {
        return 'cloud-rain';
    } elseif (strpos($condition, 'snow') !== false || strpos($condition, 'sleet') !== false) {
        return 'snowflake';
    } elseif (strpos($condition, 'thunder') !== false || strpos($condition, 'storm') !== false) {
        return 'bolt';
    } elseif (strpos($condition, 'fog') !== false || strpos($condition, 'mist') !== false) {
        return 'smog';
    } else {
        return 'cloud-sun';
    }
}