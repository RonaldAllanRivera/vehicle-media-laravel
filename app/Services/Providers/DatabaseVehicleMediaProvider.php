<?php

namespace App\Services\Providers;

use App\Contracts\VehicleMediaProvider;
use App\Models\Vehicle;

class DatabaseVehicleMediaProvider implements VehicleMediaProvider
{
    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array
    {
        $slug = $this->slug("{$year} {$make} {$model} {$trim}");
        $vehicle = Vehicle::query()->where('slug', $slug)->with(['images' => function ($q) {
            $q->orderBy('id');
        }])->first();

        if (! $vehicle) {
            return [
                'status' => 'error',
                'error' => [
                    'code' => 404,
                    'message' => 'Vehicle not found in local database.',
                ],
            ];
        }

        $limit = (int) ($options['limit'] ?? (int) config('vehicle_media.images_per_call', 10));

        $images = [
            'exterior' => [],
            'interior' => [],
            'colors' => [],
        ];

        foreach ($vehicle->images as $img) {
            if (! isset($images[$img->category])) {
                continue;
            }
            if (count($images[$img->category]) < $limit) {
                $images[$img->category][] = $img->url;
            }
        }

        return [
            'status' => 'success',
            'data' => [
                'year' => $vehicle->year,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'trim' => $vehicle->trim,
                'images' => $images,
            ],
        ];
    }

    private function slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '-', $value) ?? '';
        return trim($value, '-');
    }
}
