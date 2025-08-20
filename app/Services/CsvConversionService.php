<?php

namespace App\Services;

class CsvConversionService
{
    private string $enclosure = '"';
    private string $escape = '\\';
    private ?string $delimiter = null;
    private bool $autoDetectDelimiter = true;
    private bool $skipEmptyLines = true;

    public function configure(array $options = []): self
    {
        $this->enclosure = $options['enclosure'] ?? $this->enclosure;
        $this->escape = $options['escape'] ?? $this->escape;
        $this->delimiter = $options['delimiter'] ?? $this->delimiter;
        $this->autoDetectDelimiter = $options['autoDetectDelimiter'] ?? $this->autoDetectDelimiter;
        $this->skipEmptyLines = $options['skipEmptyLines'] ?? $this->skipEmptyLines;

        return $this;
    }

    public function convertToJson($csvStream): array
    {
        $tree = [];
        $count = 0;

        $delimiter = $this->delimiter ?? ($this->autoDetectDelimiter ? $this->detectDelimiterAndRewind($csvStream) : ',');

        $headers = null;
        while (($row = fgetcsv($csvStream, 0, $delimiter, $this->enclosure, $this->escape)) !== false) {
            if ($this->skipEmptyLines && count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            if ($headers === null) {
                $row[0] = $row[0] ?? '';
                $row[0] = preg_replace('/^\xEF\xBB\xBF/u', '', $row[0]);
                $headers = array_map(fn ($h) => trim((string) $h), $row);

                continue;
            }

            $item = $this->buildItem($headers, $row);

            $this->mergeAutoTree($tree, $item);

            $count++;
        }

        return [$tree, $count];
    }

    private function buildItem(array $headers, array $row): array
    {
        $item = [];
        foreach ($headers as $i => $header) {
            if ($header === '' || !array_key_exists($i, $row)) {
                continue;
            }
            $this->setPath($item, $header, $row[$i]);
        }

        return $item;
    }

    private function setPath(array &$array, string $header, $value): void
    {
        $segments = preg_split('/[\s._-]+/', trim($header)) ?: ['field'];
        $ref = &$array;
        $lastIndex = count($segments) - 1;

        foreach ($segments as $i => $segment) {
            $segment = $segment ?: 'field';
            $isLast = $i === $lastIndex;
            $key = ctype_digit($segment) ? (int) $segment : $segment;

            if ($isLast) {
                $ref[$key] = $value;
            } else {
                if (!isset($ref[$key]) || !is_array($ref[$key])) {
                    $ref[$key] = [];
                }
                $ref = &$ref[$key];
            }
        }
    }

    private function mergeAutoTree(array &$tree, array $item): void
    {
        foreach ($item as $key => $value) {
            if (!isset($tree[$key])) {
                $tree[$key] = $value;

                continue;
            }

            if (!is_array($tree[$key])) {
                $tree[$key] = [$tree[$key]];
            }

            if (is_array($value)) {
                $merged = false;
                foreach ($tree[$key] as &$existing) {
                    if ($this->similarStructure($existing, $value)) {
                        $this->mergeAutoTree($existing, $value);
                        $merged = true;
                        break;
                    }
                }
                if (!$merged) {
                    $tree[$key][] = $value;
                }
            } else {
                $tree[$key][] = $value;
            }
        }
    }

    private function similarStructure($a, $b): bool
    {
        if (!is_array($a) || !is_array($b)) {
            return false;
        }

        return count(array_intersect(array_keys($a), array_keys($b))) > 0;
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
}
