<?php

namespace App\Services;

use App\Http\DTO\LocationDTO;
use App\Http\DTO\WeatherDTO;
use App\Http\DTO\WeatherForecastDTO;
use App\Services\Clients\OpenWeatherMapClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

readonly class WeatherService
{
    public function __construct(
        private OpenWeatherMapClient $openWeatherMapClient
    ) {}

    public function getCurrentWeather(WeatherDTO $weatherDTO): ?array
    {
        $cacheKey = $this->generateCacheKey('weather_', $weatherDTO->toArray());

        return Cache::remember($cacheKey, now()->addHour(), function () use ($weatherDTO) {
            try {
                return $this->openWeatherMapClient->getCurrentWeather($weatherDTO);

            } catch (\Exception $e) {
                Log::error('Weather service error: ' . $e->getMessage(), [
                    'dto' => $weatherDTO->toArray()
                ]);

                return null;
            }
        });
    }

    public function getForecast(WeatherForecastDTO $weatherForecastDTO): ?array
    {
        $cacheKey = $this->generateCacheKey('forecast_', $weatherForecastDTO->toArray());

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($weatherForecastDTO) {
            try {
                return $this->openWeatherMapClient->getForecast($weatherForecastDTO);

            } catch (\Exception $e) {
                Log::error('Weather forecast service error: ' . $e->getMessage(), [
                    'dto' => $weatherForecastDTO->toArray()
                ]);

                return null;
            }
        });
    }

    public function searchLocations(LocationDTO $locationDTO): ?array
    {
        $cacheKey = 'location_search_' . md5($locationDTO->query . $locationDTO->limit);

        return Cache::remember($cacheKey, now()->addHour(), function() use ($locationDTO) {
            try {
                return $this->openWeatherMapClient->searchLocations($locationDTO);

            } catch (\Exception $e) {
                Log::error('Location search service error: ' . $e->getMessage(), [
                    'dto' => $locationDTO->toArray()
                ]);

                return null;
            }
        });
    }

    private function generateCacheKey(string $prefix, array $params): string
    {
        return $prefix . md5(serialize($params));
    }
}
