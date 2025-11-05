<?php

return [
    // Base URL for Vehicle Databases API
    'base' => env('VEHICLE_DB_BASE', 'https://api.vehicledatabases.com'),

    // API version to target (v1 or v2). Keep switchable without code changes.
    'version' => env('VEHICLE_DB_VERSION', 'v1'),

    // Secret API Key. Store only in .env.
    'api_key' => env('VEHICLE_DB_API_KEY', ''),

    // HTTP client tuning
    'timeout' => (int) env('VEHICLE_DB_TIMEOUT', 12),           // seconds
    'retries' => (int) env('VEHICLE_DB_RETRIES', 2),            // retry attempts on failure
    'sleep_ms' => (int) env('VEHICLE_DB_SLEEP_MS', 350),        // backoff base in milliseconds

    // Business rules
    'images_per_call' => (int) env('VEHICLE_DB_DEFAULT_IMAGES_PER_CALL', 10),

    // Caching (per criteria)
    'cache_ttl' => (int) env('VEHICLE_DB_CACHE_TTL', 3600),     // seconds

    // Data source: api | db | json
    'source' => env('VEHICLE_MEDIA_SOURCE', 'json'),

    // Base URL for local JSON mock API (e.g., http://127.0.0.1:8000)
    'json_base' => env('VEHICLE_MEDIA_JSON_BASE', env('APP_URL', 'http://127.0.0.1:8000')),
];
