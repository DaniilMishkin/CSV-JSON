<?php

namespace App\Jobs;

use App\Enums\JSONStructures;
use App\Models\Upload;
use App\Repositories\UploadRepository;
use App\Services\CsvConversion\CsvConversionContext;
use App\Services\CsvConversion\Strategies\HeaderBasedFlatStrategy;
use App\Services\CsvConversion\Strategies\TreeByDotHeadersStrategy;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessCsvUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Upload $upload
    ) {}

    public function handle(UploadRepository $uploadRepository): void
    {
        $uploadRepository->markProcessing($this->upload);

        try {
            $csvStream = Storage::readStream($this->upload->csv_path);
            if ($csvStream === false) {
                throw new \RuntimeException('Cannot open CSV stream');
            }

            $strategy = match ($this->upload->conversion_strategy) {
                JSONStructures::Flat => new HeaderBasedFlatStrategy(),
                JSONStructures::Tree => new TreeByDotHeadersStrategy(),
            };

            $context = new CsvConversionContext($strategy);
            [$records, $count] = $context->convert($csvStream);

            $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $jsonPath = 'uploads/json/'.$this->upload->name.'_'.Carbon::now().'.json';
            Storage::put($jsonPath, $json);

            $uploadRepository->markDone($this->upload, $jsonPath);
        } catch (\Throwable $e) {
            $uploadRepository->markError($this->upload);
        }
    }
}
