<?php

namespace App\Services\CsvConversion\Strategies;

class TreeByDotHeadersStrategy implements CsvConversionStrategy
{
    public function convert($csvStream): array
    {
        $headers = null;
        $records = [];
        $count = 0;

        while (($row = fgetcsv($csvStream)) !== false) {
            if ($headers === null) {
                $headers = $row;

                continue;
            }

            $item = [];
            foreach ($headers as $index => $header) {
                $this->setByDot($item, $header, $row[$index] ?? null);
            }
            $records[] = $item;
            $count++;
        }

        return [$records, $count];
    }

    private function setByDot(array &$array, string $path, $value): void
    {
        $segments = explode('.', $path);
        $ref = &$array;
        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }
        $ref = $value;
    }
}
