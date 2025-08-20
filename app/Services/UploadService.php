<?php

namespace App\Services;

use App\Enums\ConversionStatuses;
use App\Jobs\ProcessCsvUploadJob;
use App\Models\Upload;
use App\Repositories\UploadRepository;
use App\Services\Storage\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class UploadService extends AbstractService
{
    public function __construct(
        private readonly UploadRepository $uploadRepository,
        private readonly FileStorageService $fileStorageService,
        private readonly CsvConversionService $csvConversionService,
    ) {}

    /**
     * @throws ValidationException
     */
    public function create(UploadedFile $file, string $name, bool $isPrivate): Upload
    {
        //        if ($this->uploadRepository->userHasRecentUpload($this->user())) {
        //            throw ValidationException::withMessages([
        //                'file' => 'Upload limit: 1 file per 5 minutes',
        //            ])->status(429);
        //        }

        if ($this->user()->created_at === null || $this->user()->created_at->lt(now()->subDays(10)) === false) {
            throw ValidationException::withMessages([
                'file' => 'Account must be at least 10 days old',
            ])->status(403);
        }

        $upload = $this->uploadRepository->create($name, $isPrivate, $this->user()->id);
        $path = $this->fileStorageService->store(Upload::ORIGINAL_DIR, $file);
        $this->uploadRepository->updatePathOriginal($upload, $path);

        ProcessCsvUploadJob::dispatch($upload);

        return $upload;
    }

    /**
     * @throws Throwable
     */
    public function convert(Upload $upload): void
    {
        $this->uploadRepository->updateStatus($upload, ConversionStatuses::Processing);

        try {
            $csvStream = $this->fileStorageService->openCsvStream($upload->path_original);
            [$records] = $this->csvConversionService->convertToJson($csvStream);

            $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new \RuntimeException('Failed to encode JSON');
            }

            $jsonPath = $this->fileStorageService
                ->upload(sprintf('%s/%s.json', Upload::CONVERTED_DIR, Str::random(40)), $json);

            $this->markDone($upload, $jsonPath);
        } catch (Throwable $e) {
            $this->markError($upload, $e->getMessage());
        }
    }

    public function download(Upload $upload): StreamedResponse
    {
        $this->checkAccess('download', $upload);

        return $this->fileStorageService->download($upload->path_converted, pathinfo($upload->name, PATHINFO_FILENAME).'.json');
    }

    private function markDone(Upload $upload, string $jsonPath): void
    {
        $this->uploadRepository->updateStatus($upload, ConversionStatuses::Done);
        $this->uploadRepository->updatePathConverted($upload, $jsonPath);
    }

    private function markError(Upload $upload, string $error): void
    {
        $this->uploadRepository->updateStatus($upload, ConversionStatuses::Error);
        $this->uploadRepository->updateErrorMessage($upload, $error);
    }
}
