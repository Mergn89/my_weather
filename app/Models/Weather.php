<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    protected $table = 'weather';

    protected $fillable = [
        'city',
        'lat',
        'lon',
        'units',
        'weather_data',
        'cache_key'
    ];

    protected $casts = [
        'weather_data' => 'array',
        'lat' => 'float',
        'lon' => 'float'
    ];

    public function formattedData(): Attribute
    {
        return Attribute::make(
            get: function () {
                $data = $this->weather_data;

                return [
                    'temp' => $data['main']['temp'] ?? null,
                    'feels_like' => $data['main']['feels_like'] ?? null,
                    'description' => $data['weather'][0]['description'] ?? null,
                    'icon' => $data['weather'][0]['icon'] ?? null,
                    'wind_speed' => $data['wind']['speed'] ?? null,
                    'wind_deg' => $data['wind']['deg'] ?? null,
                    'pressure' => $data['main']['pressure'] ?? null,
                    'humidity' => $data['main']['humidity'] ?? null,
                    'rain' => $data['rain']['1h'] ?? 0,
                    'pop' => $data['pop'] ?? 0
                ];
            }
        );
    }
}
