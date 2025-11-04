<?php

namespace App\Http\Controllers;

use App\Services\MediaDownloadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MediaDownloadController extends Controller
{
    public function __construct(private readonly MediaDownloadService $service)
    {
    }

    /**
     * Handle bulk download of selected image URLs into a ZIP.
     */
    public function bulkDownload(Request $request): HttpResponse|RedirectResponse
    {
        $validated = $request->validate([
            'urls' => ['required', 'array', 'min:1'],
            'urls.*' => ['url'],
            'year' => ['nullable'],
            'make' => ['nullable'],
            'model' => ['nullable'],
            'trim' => ['nullable'],
        ]);

        try {
            $label = trim(implode(' ', array_filter([
                $validated['year'] ?? null,
                $validated['make'] ?? null,
                $validated['model'] ?? null,
            ])));

            $zipBaseName = $label !== '' ? str_replace(' ', '-', strtolower($label)) : null;
            $zipPath = $this->service->createZipFromUrls($validated['urls'], $zipBaseName);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            Log::error('Bulk download failed', ['error' => $e->getMessage()]);

            return back()->withErrors([
                'bulk_download' => 'Bulk download failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Download a single image by proxying the remote URL.
     */
    public function download(Request $request): HttpResponse|RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
        ]);

        try {
            $url = $validated['url'];
            $response = Http::timeout(15)->get($url);
            if (! $response->successful()) {
                return back()->withErrors(['download' => 'Remote server responded with status '.$response->status()]);
            }

            $parsed = parse_url($url);
            $name = basename($parsed['path'] ?? 'image');
            $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg';
            $safe = Str::slug(pathinfo($name, PATHINFO_FILENAME)) ?: 'image';
            $filename = $safe.'.'.$ext;

            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type', 'application/octet-stream'),
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            Log::error('Single download failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['download' => 'Download failed: '.$e->getMessage()]);
        }
    }
}
