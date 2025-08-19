<?php

declare(strict_types=1);

namespace App\Http\Responses\Upload;

use App\Http\Responses\EnumResponse;
use Spatie\LaravelData\Data;

class UploadListItemResponse extends Data
{
    public int $id;
    public string $name;
    public EnumResponse $status;
    public EnumResponse $conversionStrategy;
    public string $authorName;
}
