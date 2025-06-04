<?php

namespace App\Http\Controllers;

use App\Http\DTO\LocationDTO;
use App\Http\DTO\WeatherDTO;
use App\Http\DTO\WeatherForecastDTO;
use App\Http\Requests\CurrentWeatherRequest;
use App\Http\Requests\WeatherForecastRequest;
use App\Http\Requests\WeatherSearchRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\WeatherForecastResource;
use App\Http\Resources\WeatherResource;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Stevebauman\Location\Facades\Location;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {}

    public function current(CurrentWeatherRequest $request): JsonResponse
    {
        $weatherRequestDTO = WeatherDTO::fromArray($request->validated());
        $this->resolveLocation($request, $weatherRequestDTO);

        if (!$weather = $this->weatherService->getCurrentWeather($weatherRequestDTO)) {
            return response()->json(['error' => 'Данные о погоде недоступны'], 503);
        }

        return (new WeatherResource($weather))->response();
    }

    public function forecast(WeatherForecastRequest $weatherForecastRequest): JsonResponse
    {
        $forecastRequestDTO = WeatherForecastDTO::fromArray($weatherForecastRequest->validated());
        $this->resolveLocation($weatherForecastRequest, $forecastRequestDTO);

        if (!$forecast = $this->weatherService->getForecast($forecastRequestDTO)) {
            return response()->json(['error' => 'Данные прогноза недоступны'], 503);
        }

        return WeatherForecastResource::collection($forecast['list'])->response();
    }

    public function search(WeatherSearchRequest $request): JsonResponse
    {
        $locationSearchDTO = LocationDTO::fromArray($request->validated());
        $results = $this->weatherService->searchLocations($locationSearchDTO);

        return $results
            ? LocationResource::collection($results)->response()
            : response()->json(['error' => 'Поиск местоположения недоступен'], 503);
    }

    private function resolveLocation($request, $dto): void
    {
        if (!$dto->hasLocationData()) {
            if ($location = Location::get($request->ip())) {
                $dto->lat = $location->latitude;
                $dto->lon = $location->longitude;
            } else {
                $dto->city = config('services.openweather.city');
            }
        }
    }
}
