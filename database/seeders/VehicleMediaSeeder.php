<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;

class VehicleMediaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'year' => 2017,
            'make' => 'Acura',
            'model' => 'ILX',
            'trim' => 'Base 4dr Sedan Automatic',
            'images' => [
                'exterior' => [
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303031.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303032.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303033.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303035.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303036.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303231.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303234.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/ext-3231303235.jpg',
                ],
                'interior' => [
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3031333530.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3031333531.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3031333532.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303131.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303132.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303133.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303138.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303238.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/gallery/2017/acura/ilx/base-4dr-sedan-automatic/int-3231303434.jpg',
                ],
                'colors' => [
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/bellanova-white-pearl.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/catalina-blue-pearl.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/crystal-black-pearl.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/lunar-silver-metallic.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/modern-steel-metallic.jpg',
                    'https://vhr.nyc3.cdn.digitaloceanspaces.com/vehiclemedia/Colors/2017/acura/ilx/base-4dr-sedan/san-marino-red.jpg',
                ],
            ],
        ];

        $slug = $this->slug("{$data['year']} {$data['make']} {$data['model']} {$data['trim']}");

        $vehicle = Vehicle::firstOrCreate([
            'slug' => $slug,
        ], [
            'year' => $data['year'],
            'make' => $data['make'],
            'model' => $data['model'],
            'trim' => $data['trim'],
        ]);

        foreach (['exterior', 'interior', 'colors'] as $category) {
            foreach ($data['images'][$category] as $url) {
                VehicleImage::firstOrCreate([
                    'vehicle_id' => $vehicle->id,
                    'url' => $url,
                ], [
                    'category' => $category,
                ]);
            }
        }
    }

    private function slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '-', $value) ?? '';
        return trim($value, '-');
    }
}
