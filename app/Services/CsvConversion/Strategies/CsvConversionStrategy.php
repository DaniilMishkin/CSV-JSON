<?php

namespace App\Services\CsvConversion\Strategies;

interface CsvConversionStrategy
{
    public function convert($csvStream): array;
}
