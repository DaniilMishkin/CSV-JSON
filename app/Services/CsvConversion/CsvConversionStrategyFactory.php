<?php

namespace App\Services\CsvConversion;

use App\Enums\JSONStructures;
use App\Services\CsvConversion\Strategies\CsvConversionStrategy;
use App\Services\CsvConversion\Strategies\HeaderBasedFlatStrategy;
use App\Services\CsvConversion\Strategies\TreeByDotHeadersStrategy;

class CsvConversionStrategyFactory
{
    public function make(JSONStructures $strategy): CsvConversionStrategy
    {
        return match ($strategy) {
            JSONStructures::Flat => new HeaderBasedFlatStrategy(),
            JSONStructures::Tree => new TreeByDotHeadersStrategy(),
        };
    }
}
