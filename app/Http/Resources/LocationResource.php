<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'name' => data_get($this, 'name'),
            'country' => data_get($this, 'country'),
            'state' => data_get($this, 'state'),
            'coordinates' => [
                'latitude' => data_get($this, 'lat'),
                'longitude' => data_get($this, 'lon'),
            ],
        ];
    }
}
