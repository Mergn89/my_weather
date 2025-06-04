<?php

namespace App\Http\DTO;

class WeatherForecastDTO extends BaseWeatherDTO
{
    public int $cnt;

    public static function fromArray(array $data): static
    {
        $dto = parent::fromArray($data);
        $dto->cnt = $data['cnt'] ?? config('services.openweather.forecast_cnt', 8);
        return $dto;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'cnt' => $this->cnt,
        ]);
    }
}
