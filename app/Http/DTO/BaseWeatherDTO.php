<?php

namespace App\Http\DTO;

abstract class BaseWeatherDTO
{
    public ?string $city;
    public ?float $lat;
    public ?float $lon;
    public string $units;

    public static function fromArray(array $data): static
    {
        $dto = new static();
        $dto->city = isset($data['city']) ? trim($data['city']) : null;
        $dto->lat = isset($data['lat']) ? (float) $data['lat'] : null;
        $dto->lon = isset($data['lon']) ? (float) $data['lon'] : null;
        $dto->units = $data['units'] ?? config('services.openweather.units', 'metric');
        return $dto;
    }

    public function hasCoordinates(): bool
    {
        return $this->lat !== null && $this->lon !== null;
    }

    public function hasCity(): bool
    {
        return $this->city !== null && trim($this->city) !== '';
    }

    public function hasLocationData(): bool
    {
        return $this->hasCoordinates() || $this->hasCity();
    }

    public function toArray(): array
    {
        return array_filter([
            'city' => $this->city,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'units' => $this->units,
        ], fn($value) => $value !== null && $value !== '');
    }
}
