<?php

namespace App\Services\Providers;

use App\Contracts\VehicleMediaProvider;
use App\Services\VehicleMediaClient;

class ApiVehicleMediaProvider implements VehicleMediaProvider
{
    public function __construct(private VehicleMediaClient $client)
    {
    }

    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array
    {
        return $this->client->getMedia($year, $make, $model, $trim, $options);
    }
}
