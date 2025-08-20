<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Services\UploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCsvUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Upload $upload
    ) {}

    public function handle(UploadService $uploadService): void
    {
        $uploadService->convertToJson($this->upload);
    }
}
