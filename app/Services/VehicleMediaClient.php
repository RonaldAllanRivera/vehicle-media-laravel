<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Throwable;

class VehicleMediaClient
{
    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array
    {
        $base = rtrim(Config::get('vehicle_media.base'), '/');
        $version = trim(Config::get('vehicle_media.version', 'v2'), '/');
        $timeout = (int) Config::get('vehicle_media.timeout', 12);
        $retries = (int) Config::get('vehicle_media.retries', 2);
        $sleepMs = (int) Config::get('vehicle_media.sleep_ms', 350);
        $ttl = (int) Config::get('vehicle_media.cache_ttl', 3600);
        $apiKey = Config::get('vehicle_media.api_key');
        $limit = (int) ($options['limit'] ?? (int) Config::get('vehicle_media.images_per_call', 10));

        $path = implode('/', [
            'vehicle-media',
            $version,
            rawurlencode((string) $year),
            rawurlencode($this->slug($make)),
            rawurlencode($this->slug($model)),
            rawurlencode($this->slug($trim)),
        ]);

        $url = $base . '/' . $path;

        $cacheKey = 'veh_media:' . md5(implode('|', [$url, $limit]));

        return Cache::remember($cacheKey, $ttl, function () use ($url, $timeout, $retries, $sleepMs, $apiKey, $limit) {
            try {
                $response = Http::withHeaders([
                        'x-AuthKey' => $apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->timeout($timeout)
                    ->retry($retries, $sleepMs)
                    ->get($url);

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
            } catch (Throwable $e) {
                return [
                    'status' => 'error',
                    'error' => [
                        'code' => 0,
                        'message' => $e->getMessage(),
                    ],
                ];
            }
        });
    }

    private function slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '-', $value) ?? '';
        return trim($value, '-');
    }
}

