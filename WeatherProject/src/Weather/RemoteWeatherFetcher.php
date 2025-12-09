<?php

namespace App\Weather;

class RemoteWeatherFetcher implements WeatherFetcherInterface {

    public function fetch(string $city): ?WeatherInfo {
        $params = http_build_query(['city' => $city]);

        $url = 'https://downloads.codingcoursestv.eu/056%20-%20php/weather/weather.php?' . $params;

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: PHP Weather App\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $content = @file_get_contents($url, false, $context);

        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        if ($data === null || empty($data['city']) || empty($data['temperature']) || empty($data['weather'])) {
            return null;
        }

        return new WeatherInfo($data['city'], $data['temperature'], $data['weather']);
    }
}