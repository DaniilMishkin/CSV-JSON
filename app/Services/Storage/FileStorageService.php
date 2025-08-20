<?php

declare(strict_types=1);

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    public function store(string $directory, UploadedFile $file, ?string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');

        return $file->store($directory, $disk);
    }

    public function upload(string $path, mixed $content, ?string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        Storage::disk($disk)->put($path, $content);

        return $path;
    }

    public function delete(string $path, ?string $disk = null): void
    {
        $disk = $disk ?? config('filesystems.default');
        Storage::disk($disk)->delete($path);
    }

    public function url(string $path, ?string $disk = null): string
    {
        $disk = $disk ?? config('filesystems.default');

        return Storage::disk($disk)->url($path);
    }

    public function download(string $path, string $name, ?string $disk = null): StreamedResponse
    {
        $disk = $disk ?? config('filesystems.default');

        return Storage::disk($disk)->download($path, $name);
    }

    public function openCsvStream(string $path)
    {
        $stream = Storage::readStream($path);
        if ($stream === false) {
            throw new \RuntimeException('Cannot open CSV stream');
        }

        return $stream;
    }
}
