<?php

namespace App\Services\Clients;

use App\Http\DTO\SearchLocationDTO;
use App\Http\DTO\WeatherCurrentDTO;
use App\Http\DTO\WeatherForecastDTO;
use App\Http\DTO\BaseWeatherDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWeatherMapClient
{
    protected string $apiKey;
    protected string $weatherUrl;
    protected string $geocodingUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        $this->weatherUrl = config('services.openweather.weather_url');
        $this->geocodingUrl = config('services.openweather.geocoding_url');
        $this->timeout = config('services.openweather.timeout', 10);
    }

    public function getCurrent(WeatherCurrentDTO $weatherRequestDTO): ?array
    {
        try {
            $queryParams = array_merge([
                'appid' => $this->apiKey,
                'units' => $weatherRequestDTO->units,
                'lang' => config('services.openweather.language', 'ru')
            ], $this->prepareLocationParams($weatherRequestDTO));

            $response = Http::timeout($this->timeout)
                ->retry(3, 1000)
                ->get($this->weatherUrl, $queryParams);

            if ($response->failed()) {
                Log::warning('OpenWeatherMap API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $queryParams
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['main']) || !isset($data['weather'])) {
                Log::warning('Invalid OpenWeatherMap API response structure', ['data' => $data]);
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('OpenWeatherMap weather API error: ' . $e->getMessage(), [
                'dto' => $weatherRequestDTO->toArray()
            ]);
            return null;
        }
    }

    public function searchLocations(SearchLocationDTO $locationSearchDTO): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry(3, 1000)
                ->get("{$this->geocodingUrl}/direct", [
                    'q' => trim($locationSearchDTO->query),
                    'limit' => $locationSearchDTO->limit,
                    'appid' => $this->apiKey
                ]);

            if ($response->failed()) {
                Log::warning('OpenWeatherMap Geocoding API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'query' => $locationSearchDTO->query
                ]);
                return null;
            }

            $data = $response->json();

            if (!is_array($data)) {
                Log::warning('Invalid OpenWeatherMap Geocoding API response', ['data' => $data]);
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('OpenWeatherMap geocoding API error: ' . $e->getMessage(), [
                'dto' => $locationSearchDTO->toArray()
            ]);
            return null;
        }
    }

    public function getForecast(WeatherForecastDTO $weatherForecastDTO): ?array
    {
        try {
            $queryParams = array_merge([
                'appid' => $this->apiKey,
                'units' => $weatherForecastDTO->units,
                'cnt' => $weatherForecastDTO->cnt,
                'lang' => config('services.openweather.language', 'ru')
            ], $this->prepareLocationParams($weatherForecastDTO));

            $forecastUrl = str_replace('/weather', '/forecast', $this->weatherUrl);

            $response = Http::timeout($this->timeout)
                ->retry(3, 1000)
                ->get($forecastUrl, $queryParams);

            if ($response->failed()) {
                Log::warning('OpenWeatherMap Forecast API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $queryParams
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['list']) || !is_array($data['list'])) {
                Log::warning('Invalid OpenWeatherMap Forecast API response structure', ['data' => $data]);
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('OpenWeatherMap forecast API error: ' . $e->getMessage(), [
                'dto' => $weatherForecastDTO->toArray()
            ]);
            return null;
        }
    }

    private function prepareLocationParams(BaseWeatherDTO $weatherDTO): array
    {
        if ($weatherDTO->hasCoordinates()) {
            return [
                'lat' => $weatherDTO->lat,
                'lon' => $weatherDTO->lon
            ];
        }

        if ($weatherDTO->hasCity()) {
            return ['q' => trim($weatherDTO->city)];
        }

        return [];
    }
}

