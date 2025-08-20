<?php

namespace App\Services;

use App\Http\Requests\StoreUploadRequest;
use App\Jobs\ProcessCsvUploadJob;
use App\Models\Upload;
use App\Repositories\UploadRepository;
use App\Services\CsvConversion\CsvConversionStrategyFactory;
use App\Services\Storage\FileStorageService;
use Illuminate\Support\Str;

readonly class UploadService
{
    public function __construct(
        private UploadRepository $uploadRepository,
        private FileStorageService $fileStorageService,
        private CsvConversionStrategyFactory $strategyFactory,
    ) {}

    public function createAndConvert(StoreUploadRequest $request): Upload
    {
        $upload = $this->uploadRepository->create($request);

        $path = $this->fileStorageService->store(
            Upload::ORIGINAL_DIR,
            $request->file,
        );

        $upload->path_original = $path;
        $upload->save();

        ProcessCsvUploadJob::dispatch($upload);

        return $upload;
    }

    public function convertToJson(Upload $upload): void
    {
        $this->uploadRepository->markProcessing($upload);

        try {
            $csvStream = $this->fileStorageService->openCsvStream($upload->path_original);

            $strategy = $this->strategyFactory->make($upload->conversion_strategy);
            [$records] = $strategy->convert($csvStream);

            $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new \RuntimeException('Failed to encode JSON');
            }

            $jsonPath = $this->fileStorageService->upload(sprintf('%s/%s.json', Upload::CONVERTED_DIR, Str::random(40)), $json);

            $this->uploadRepository->markDone($upload, $jsonPath);
        } catch (\Throwable $e) {
            $this->uploadRepository->markError($upload, $e->getMessage());
        }
    }
}
