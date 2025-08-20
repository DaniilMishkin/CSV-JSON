<?php

namespace App\Services\CsvConversion\Strategies;

class HeaderBasedFlatStrategy implements CsvConversionStrategy
{
    public function convert($csvStream): array
    {
        $records = [];
        $count = 0;

        $headerLine = fgets($csvStream);
        if ($headerLine === false) {
            return [$records, $count];
        }
        $headerLine = trim($headerLine);

        while (($line = fgets($csvStream)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $records[] = [
                $headerLine => $line
            ];
            $count++;
        }

        return [$records, $count];
    }
}
