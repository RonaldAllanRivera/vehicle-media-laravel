<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaDownloadController;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/media/bulk-download', [MediaDownloadController::class, 'bulkDownload'])
        ->name('media.bulk-download');
    Route::get('/admin/media/download', [MediaDownloadController::class, 'download'])
        ->name('media.download');
});
