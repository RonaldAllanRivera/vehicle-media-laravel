<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MockVehicleMediaController extends Controller
{
    public function show(Request $request, string $version, int $year, string $make, string $model, string $trim)
    {
        $slug = $this->slug("{$year} {$make} {$model} {$trim}");
        $vehicle = Vehicle::query()->where('slug', $slug)->with('images')->first();

        if (! $vehicle) {
            return response()->json([
                'status' => 'error',
                'error' => [
                    'code' => 404,
                    'message' => 'Vehicle not found in mock DB',
                ],
            ], 404);
        }

        $images = [
            'exterior' => [],
            'interior' => [],
            'colors' => [],
        ];
        foreach ($vehicle->images as $img) {
            if (isset($images[$img->category])) {
                $images[$img->category][] = $img->url;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'year' => $vehicle->year,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'trim' => $vehicle->trim,
                'images' => $images,
            ],
        ]);
    }

    private function slug(string $value): string
    {
        $value = strtolower($value);
        $value = preg_replace('~[^a-z0-9]+~', '-', $value) ?? '';
        return trim($value, '-');
    }
}
