<?php

namespace App\Services\CsvConversion\Strategies;


class TreeByDotHeadersStrategy implements CsvConversionStrategy
{
    private const string ENCLOSURE = '"';
    private const string ESCAPE = '\\';

    public function convert($csvStream): array
    {
        $records = [];
        $count = 0;

        $delimiter = $this->detectDelimiterAndRewind($csvStream);

        $headers = null;
        while (($row = fgetcsv($csvStream, 0, $delimiter, self::ENCLOSURE, self::ESCAPE)) !== false) {
            if ($headers === null) {
                if (isset($row[0])) {
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/u', '', (string) $row[0]);
                }
                $headers = array_map(static fn($h) => trim((string) $h), $row);
                continue;
            }

            $item = [];
            foreach ($headers as $i => $header) {
                if ($header === '' || !array_key_exists($i, $row)) {
                    continue;
                }
                $this->setByDot($item, $header, $row[$i]);
            }

            $records[] = $item;
            $count++;
        }

        return [$records, $count];
    }

    private function detectDelimiterAndRewind($stream): string
    {
        $pos = ftell($stream);
        $line = fgets($stream);
        if ($line === false) {
            fseek($stream, $pos);
            return ',';
        }

        $line = preg_replace('/^\xEF\xBB\xBF/u', '', $line);
        fseek($stream, $pos);

        $candidates = [',', ';', "\t", '|'];
        $best = ',';
        $bestCount = -1;
        foreach ($candidates as $cand) {
            $count = substr_count($line, $cand);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $cand;
            }
        }
        return $best;
    }

    private function setByDot(array &$array, string $path, $value): void
    {
        $segments = array_filter(
            array_map('trim', explode('.', $path)),
            static fn($s) => $s !== ''
        );

        $ref = &$array;
        $lastIndex = count($segments) - 1;

        foreach ($segments as $i => $segment) {
            $isLast = $i === $lastIndex;

            $key = ctype_digit($segment) ? (int) $segment : $segment;

            if ($isLast) {
                $ref[$key] = $value;
                return;
            }
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }
            $ref = &$ref[$key];
        }
    }
}
