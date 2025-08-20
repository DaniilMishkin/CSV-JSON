<?php

namespace App\Http\Requests;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class StoreUploadRequest extends Data
{
    #[File]
    public UploadedFile $file;
    #[Max(Upload::RULE_MAX_NAME)]
    public string $name;
    public bool $isPrivate;
}
