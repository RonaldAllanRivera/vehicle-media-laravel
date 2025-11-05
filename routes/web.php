<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaDownloadController;
use App\Http\Controllers\MockVehicleMediaController;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

// Public mock JSON endpoint to serve normalized media payloads from local DB
Route::get('/mock/vehicle-media/{version}/{year}/{make}/{model}/{trim}', [MockVehicleMediaController::class, 'show'])
    ->where(['year' => '[0-9]{4}'])
    ->name('mock.vehicle-media.show');

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/media/bulk-download', [MediaDownloadController::class, 'bulkDownload'])
        ->name('media.bulk-download');
    Route::get('/admin/media/download', [MediaDownloadController::class, 'download'])
        ->name('media.download');
});
