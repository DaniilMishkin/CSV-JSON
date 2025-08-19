<?php

namespace App\Enums;

use App\Traits\Enums\HasValues;

enum ConversionStatuses: string
{
    use HasValues;

    case Queued = 'queued';
    case Processing = 'processing';
    case Done = 'done';
    case Error = 'error';
}
