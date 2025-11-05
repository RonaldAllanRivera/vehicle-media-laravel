<?php

namespace App\Services;

use App\Services\Providers\ApiVehicleMediaProvider;
use App\Services\Providers\DatabaseVehicleMediaProvider;
use App\Services\Providers\JsonVehicleMediaProvider;

class VehicleMediaResolver
{
    public function __construct(
        private ApiVehicleMediaProvider $api,
        private DatabaseVehicleMediaProvider $db,
        private JsonVehicleMediaProvider $json,
    ) {}

    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array
    {
        $source = $options['source'] ?? config('vehicle_media.source', 'api');
        return match ($source) {
            'db' => $this->db->getMedia($year, $make, $model, $trim, $options),
            'json' => $this->json->getMedia($year, $make, $model, $trim, $options),
            default => $this->api->getMedia($year, $make, $model, $trim, $options),
        };
    }
}
