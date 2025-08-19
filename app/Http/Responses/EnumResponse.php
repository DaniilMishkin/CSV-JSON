<?php

namespace App\Http\Responses;

use App\Data\EnumNormalizer;
use Spatie\LaravelData\Data;

class EnumResponse extends Data
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function normalizers(): array
    {
        return array_merge([EnumNormalizer::class], parent::normalizers());
    }
}
