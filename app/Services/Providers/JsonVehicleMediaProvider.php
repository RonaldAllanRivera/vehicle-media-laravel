<?php

namespace App\Services\Providers;

use App\Contracts\VehicleMediaProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class JsonVehicleMediaProvider implements VehicleMediaProvider
{
    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array
    {
        $base = rtrim((string) Config::get('vehicle_media.json_base', config('app.url')), '/');
        $version = trim((string) Config::get('vehicle_media.version', 'v1'), '/');
        $timeout = (int) Config::get('vehicle_media.timeout', 12);
        $retries = (int) Config::get('vehicle_media.retries', 2);
        $sleepMs = (int) Config::get('vehicle_media.sleep_ms', 350);
        $limit = (int) ($options['limit'] ?? (int) Config::get('vehicle_media.images_per_call', 10));

        $path = implode('/', [
            'vehicle-media',
            $version,
            rawurlencode((string) $year),
            rawurlencode($this->slug($make)),
            rawurlencode($this->slug($model)),
            rawurlencode($this->slug($trim)),
        ]);

        // Hit the local mock endpoint: {base}/mock/{path}
        $url = $base . '/mock/' . $path;

        $response = Http::timeout($timeout)->retry($retries, $sleepMs)->get($url);
        if ($response->failed()) {
            return [
                'status' => 'error',
                'error' => [
                    'code' => $response->status(),
                    'message' => $response->json('message') ?? $response->body(),
                ],
            ];
        }

        $json = $response->json();
        $images = Arr::get($json, 'data.images', []);
        foreach (['exterior', 'interior', 'colors'] as $k) {
            if (isset($images[$k]) && is_array($images[$k])) {
                $images[$k] = array_values(array_slice($images[$k], 0, $limit));
            }
        }

        return [
            'status' => 'success',
            'data' => [
                'year' => Arr::get($json, 'data.year'),
                'make' => Arr::get($json, 'data.make'),
                'model' => Arr::get($json, 'data.model'),
                'trim' => Arr::get($json, 'data.trim'),
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
