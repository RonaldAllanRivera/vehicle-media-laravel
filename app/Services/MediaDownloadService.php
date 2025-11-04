<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class MediaDownloadService
{
    /**
     * Create a ZIP file from an array of remote image URLs.
     * Returns absolute path to the created ZIP.
     */
    public function createZipFromUrls(array $urls, ?string $zipBaseName = null): string
    {
        $urls = array_values(array_filter(array_unique($urls)));
        if (empty($urls)) {
            throw new \InvalidArgumentException('No URLs selected.');
        }

        $zipBaseName = $zipBaseName ?: ('media-' . now()->format('Ymd-His'));
        $zipFilePath = storage_path('app/tmp/' . $zipBaseName . '.zip');

        // Ensure directory exists
        if (! is_dir(dirname($zipFilePath))) {
            mkdir(dirname($zipFilePath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create ZIP archive.');
        }

        foreach ($urls as $index => $url) {
            try {
                $response = Http::timeout(15)->get($url);
                if (! $response->successful()) {
                    Log::warning('Skipping URL due to non-200 response', ['url' => $url, 'status' => $response->status()]);
                    continue;
                }
                $body = $response->body();
                if ($body === '' || $body === null) {
                    Log::warning('Skipping URL due to empty body', ['url' => $url]);
                    continue;
                }

                // Derive a safe filename
                $parsed = parse_url($url);
                $name = basename($parsed['path'] ?? ('image-' . $index));
                $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg';
                $safe = Str::slug(pathinfo($name, PATHINFO_FILENAME));
                $filename = $safe ? ($safe . '.' . $ext) : ('image-' . $index . '.' . $ext);

                // Ensure unique filename inside ZIP
                $finalName = $filename;
                $counter = 1;
                while ($zip->locateName($finalName) !== false) {
                    $finalName = $safe . '-' . $counter . '.' . $ext;
                    $counter++;
                }

                $zip->addFromString($finalName, $body);
            } catch (\Throwable $e) {
                Log::error('Error downloading URL for ZIP', ['url' => $url, 'error' => $e->getMessage()]);
                continue;
            }
        }

        $zip->close();

        return $zipFilePath;
    }
}
