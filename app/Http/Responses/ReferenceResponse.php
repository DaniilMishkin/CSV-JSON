<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Spatie\LaravelData\Data;

class ReferenceResponse extends Data
{
    public function __construct(
        public int $id,
        public ?string $name = null,
    ) {}
}
