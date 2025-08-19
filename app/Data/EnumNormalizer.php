<?php

namespace App\Data;

use App\Helpers\EnumMapper;
use Spatie\LaravelData\Normalizers\Normalizer;
use UnitEnum;

class EnumNormalizer implements Normalizer
{
    public function normalize(mixed $value): ?array
    {
        if ($value instanceof UnitEnum) {
            return EnumMapper::toArray($value);
        }

        return null;
    }
}
