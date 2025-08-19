<?php

namespace App\Services;

use App\Http\Requests\StoreUploadRequest;
use App\Jobs\ProcessCsvUploadJob;
use App\Models\Upload;
use App\Repositories\UploadRepository;
use Illuminate\Http\UploadedFile;

readonly class UploadService
{
    public function __construct(
        private UploadRepository $uploadRepository
    ) {}

    public function createAndConvert(StoreUploadRequest $request): Upload
    {
        $upload = $this->uploadRepository->create($request);

        $this->putUploadToStorage($upload, $request->file);

        ProcessCsvUploadJob::dispatch($upload);

        return $upload;
    }

    private function putUploadToStorage(Upload $upload, UploadedFile $file): bool
    {
        $path = $file->store('uploads/csv', [
            'name' => $upload->name,
        ]);

        $upload->csv_path = $path;

        return $upload->save();
    }
}
