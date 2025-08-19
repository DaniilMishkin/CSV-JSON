<?php

namespace App\Services\CsvConversion\Strategies;

class HeaderBasedFlatStrategy implements CsvConversionStrategy
{
    public function convert($csvStream): array
    {
        $records = [];
        $headers = null;
        $count = 0;

        while (($row = fgetcsv($csvStream)) !== false) {
            if ($headers === null) {
                $headers = $row;

                continue;
            }

            $records[] = $this->combineRow($headers, $row);
            $count++;
        }

        return [$records, $count];
    }

    private function combineRow(array $headers, array $row): array
    {
        $combined = [];
        foreach ($headers as $index => $key) {
            $combined[$key] = $row[$index] ?? null;
        }

        return $combined;
    }
}
