<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentWeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'city' => data_get($this, 'name'),
            'country' => data_get($this, 'sys.country'),
            'temperature' => data_get($this, 'main.temp'),
            'feels_like' => data_get($this, 'main.feels_like'),
            'wind_speed' => data_get($this, 'wind.speed'),
            'humidity' => data_get($this, 'main.humidity'),
            'pressure' => data_get($this, 'main.pressure'),
//            'units' => data_get($this->additional, 'units', 'metric'),
        ];
    }
}
