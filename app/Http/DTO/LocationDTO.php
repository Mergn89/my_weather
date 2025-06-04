<?php

namespace App\Http\DTO;

class LocationDTO
{
    public string $query;
    public int $limit;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->query = $data['query'];
        $dto->limit = $data['limit'] ?? 5;
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'limit' => $this->limit,
        ];
    }
}
