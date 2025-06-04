<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForecastWeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'datetime' => data_get($this, 'dt_txt'),
            'timestamp' => data_get($this, 'dt'),
            'temperature' => data_get($this, 'main.temp'),
            'feels_like' => data_get($this, 'main.feels_like'),
            'temp_min' => data_get($this, 'main.temp_min'),
            'temp_max' => data_get($this, 'main.temp_max'),
            'description' => data_get($this, 'weather.0.description'),
            'icon' => data_get($this, 'weather.0.icon'),
            'wind_speed' => data_get($this, 'wind.speed'),
            'wind_direction' => data_get($this, 'wind.deg'),
            'humidity' => data_get($this, 'main.humidity'),
            'pressure' => data_get($this, 'main.pressure'),
            'pop' => data_get($this, 'pop', 0),
            'visibility' => data_get($this, 'visibility'),
            'clouds' => data_get($this, 'clouds.all'),
        ];
    }
}
