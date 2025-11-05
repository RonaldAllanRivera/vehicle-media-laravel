<?php

namespace App\Contracts;

interface VehicleMediaProvider
{
    public function getMedia(int $year, string $make, string $model, string $trim, array $options = []): array;
}
