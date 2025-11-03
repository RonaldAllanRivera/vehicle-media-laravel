<?php

namespace Tests\Unit;

use App\Services\VehicleMediaClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VehicleMediaClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('vehicle_media.base', 'https://api.vehicledatabases.com');
        Config::set('vehicle_media.version', 'v2');
        Config::set('vehicle_media.timeout', 2);
        Config::set('vehicle_media.retries', 0);
        Config::set('vehicle_media.sleep_ms', 0);
        Config::set('vehicle_media.images_per_call', 10);
        Config::set('vehicle_media.cache_ttl', 3600);
        Config::set('vehicle_media.api_key', 'test-key');
    }

    public function test_success_returns_normalized_data_with_limit(): void
    {
        $year = 2017; $make = 'Acura'; $model = 'ILX'; $trim = 'Base 4dr Sedan Automatic';
        $url = 'https://api.vehicledatabases.com/vehicle-media/v2/2017/acura/ilx/base-4dr-sedan-automatic';

        Http::fake([
            $url => Http::response([
                'status' => 'success',
                'data' => [
                    'year' => $year,
                    'make' => $make,
                    'model' => $model,
                    'trim' => $trim,
                    'images' => [
                        'exterior' => array_map(fn($i) => "https://cdn/ext-$i.jpg", range(1, 20)),
                        'interior' => array_map(fn($i) => "https://cdn/int-$i.jpg", range(1, 20)),
                        'colors' => array_map(fn($i) => "https://cdn/color-$i.jpg", range(1, 20)),
                    ],
                ],
            ], 200),
        ]);

        $client = new VehicleMediaClient();
        $res = $client->getMedia($year, $make, $model, $trim, ['limit' => 8]);

        $this->assertSame('success', $res['status']);
        $this->assertSame($year, $res['data']['year']);
        $this->assertCount(8, $res['data']['images']['exterior']);
        $this->assertCount(8, $res['data']['images']['interior']);
        $this->assertCount(8, $res['data']['images']['colors']);
    }

    public function test_4xx_returns_error_status(): void
    {
        $url = 'https://api.vehicledatabases.com/vehicle-media/v2/2017/acura/ilx/base-4dr-sedan-automatic';
        Http::fake([$url => Http::response(['message' => 'Bad Request'], 400)]);

        $client = new VehicleMediaClient();
        $res = $client->getMedia(2017, 'Acura', 'ILX', 'Base 4dr Sedan Automatic');

        $this->assertSame('error', $res['status']);
        $this->assertSame(400, $res['error']['code']);
    }

    public function test_5xx_returns_error_status(): void
    {
        $url = 'https://api.vehicledatabases.com/vehicle-media/v2/2017/acura/ilx/base-4dr-sedan-automatic';
        Http::fake([$url => Http::response('Server error', 502)]);

        $client = new VehicleMediaClient();
        $res = $client->getMedia(2017, 'Acura', 'ILX', 'Base 4dr Sedan Automatic');

        $this->assertSame('error', $res['status']);
        $this->assertSame(502, $res['error']['code']);
    }

    public function test_timeout_returns_error_status(): void
    {
        $url = 'https://api.vehicledatabases.com/vehicle-media/v2/2017/acura/ilx/base-4dr-sedan-automatic';
        Http::fake([$url => function () { throw new \Illuminate\Http\Client\ConnectionException('timeout'); }]);

        $client = new VehicleMediaClient();
        $res = $client->getMedia(2017, 'Acura', 'ILX', 'Base 4dr Sedan Automatic');

        $this->assertSame('error', $res['status']);
        $this->assertSame(0, $res['error']['code']);
        $this->assertStringContainsString('timeout', $res['error']['message']);
    }
}
