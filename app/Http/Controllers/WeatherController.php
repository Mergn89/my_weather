<?php

namespace App\Http\Controllers;

use App\Http\DTO\SearchLocationDTO;
use App\Http\DTO\WeatherCurrentDTO;
use App\Http\DTO\WeatherForecastDTO;
use App\Http\Requests\CurrentWeatherRequest;
use App\Http\Requests\ForecastWeatherRequest;
use App\Http\Requests\SearchLocationRequest;
use App\Http\Resources\SearchLocationResource;
use App\Http\Resources\ForecastWeatherResource;
use App\Http\Resources\CurrentWeatherResource;
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
        $weatherRequestDTO = WeatherCurrentDTO::fromArray($request->validated());
        $this->resolveLocation($request, $weatherRequestDTO);

        if (!$weather = $this->weatherService->getCurrentWeather($weatherRequestDTO)) {
            return response()->json(['error' => 'Данные о погоде недоступны'], 503);
        }

        return (new CurrentWeatherResource($weather))->response();
    }

    public function forecast(ForecastWeatherRequest $weatherForecastRequest): JsonResponse
    {
        $forecastRequestDTO = WeatherForecastDTO::fromArray($weatherForecastRequest->validated());
        $this->resolveLocation($weatherForecastRequest, $forecastRequestDTO);

        if (!$forecast = $this->weatherService->getForecastWeather($forecastRequestDTO)) {
            return response()->json(['error' => 'Данные прогноза недоступны'], 503);
        }

        return ForecastWeatherResource::collection($forecast['list'])->response();
    }

    public function location(SearchLocationRequest $request): JsonResponse
    {
        $locationSearchDTO = SearchLocationDTO::fromArray($request->validated());
        $results = $this->weatherService->searchLocation($locationSearchDTO);

        return $results
            ? SearchLocationResource::collection($results)->response()
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
