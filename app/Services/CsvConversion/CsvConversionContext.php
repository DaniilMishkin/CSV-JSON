<?php

namespace App\Services\CsvConversion;

use App\Services\CsvConversion\Strategies\CsvConversionStrategy;

class CsvConversionContext
{
    public function __construct(
        private CsvConversionStrategy $csvConversionStrategy
    ) {}

    public function setStrategy(CsvConversionStrategy $strategy): void
    {
        $this->csvConversionStrategy = $strategy;
    }

    /**
     * @param  resource                            $csvStream
     * @return array{0: array<int, mixed>, 1: int}
     */
    public function convert($csvStream): array
    {
        return $this->csvConversionStrategy->convert($csvStream);
    }
}
