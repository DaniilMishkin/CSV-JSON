<?php

namespace App\Services;

class CsvConversionService
{
    private string $enclosure = '"';
    private string $escape = '\\';
    private ?string $delimiter = null;
    private bool $autoDetectDelimiter = true;
    private bool $skipEmptyLines = true;

    private array $headerSegmentsCache = [];

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
        $this->headerSegmentsCache = [];

        while (($row = fgetcsv($csvStream, 0, $delimiter, $this->enclosure, $this->escape)) !== false) {
            if ($this->skipEmptyLines) {
                $nonEmpty = false;
                foreach ($row as $v) {
                    if ($v !== null && $v !== '') {
                        $nonEmpty = true;
                        break;
                    }
                }
                if (!$nonEmpty) {
                    continue;
                }
            }

            if ($headers === null) {
                $row[0] = $row[0] ?? '';

                if ($row[0] !== '' && str_starts_with($row[0], "\xEF\xBB\xBF")) {
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/u', '', $row[0]);
                }

                $headers = array_map(static fn ($h) => trim((string) $h), $row);
                foreach ($headers as $i => $h) {
                    $this->headerSegmentsCache[$i] = $this->splitHeader($h);
                }

                continue;
            }

            $item = $this->buildItemFast($row);

            $this->mergeAutoTreeIndexed($tree, $item);

            $count++;
        }

        $this->stripMeta($tree);

        return [$tree, $count];
    }

    private function buildItemFast(array $row): array
    {
        $item = [];
        foreach ($this->headerSegmentsCache as $i => $segments) {
            if (!array_key_exists($i, $row)) {
                continue;
            }
            $value = $row[$i];
            if ($segments === []) {
                continue;
            }
            $this->setPathWithSegments($item, $segments, $value);
        }

        return $item;
    }

    private function splitHeader(string $header): array
    {
        $header = trim($header);
        if ($header === '') {
            return [];
        }
        $segments = preg_split('/[\s._-]+/', $header) ?: [];
        foreach ($segments as &$s) {
            $s = ($s === '' ? 'field' : $s);
        }

        return $segments;
    }

    private function setPathWithSegments(array &$array, array $segments, $value): void
    {
        $ref = &$array;
        $last = count($segments) - 1;

        foreach ($segments as $i => $segment) {
            $key = ctype_digit($segment) ? (int) $segment : $segment;
            if ($i === $last) {
                $ref[$key] = $value;
            } else {
                if (!isset($ref[$key]) || !is_array($ref[$key])) {
                    $ref[$key] = [];
                }
                $ref = &$ref[$key];
            }
        }
    }

    private function mergeAutoTreeIndexed(array &$tree, array $item): void
    {
        foreach ($item as $key => $value) {
            if (!array_key_exists($key, $tree)) {
                $tree[$key] = $value;

                continue;
            }

            if (is_array($tree[$key]) && is_array($value)) {
                $this->mergeNode($tree[$key], $value);

                continue;
            }

            if (!is_array($tree[$key])) {
                $tree[$key] = [$tree[$key]];
            }
            $tree[$key][] = $value;
        }
    }

    private function mergeNode(array &$node, array $value): void
    {
        if (!isset($node['__index']) || !is_array($node['__index'])) {
            $node['__index'] = [];
            foreach ($node as $k => &$existing) {
                if ($k === '__index') {
                    continue;
                }
                if (is_array($existing)) {
                    $sig = $this->signature($existing);
                    $node['__index'][$sig][] = &$existing;
                }
            }
            unset($existing);
        }

        if (!$this->isAssoc($value)) {
            $node[] = $value;

            return;
        }

        $sig = $this->signature($value);

        if (!isset($node['__index'][$sig])) {
            $node[] = $value;
            $lastKey = array_key_last($node);
            while ($lastKey === '__index') {
                $node[] = $value;
                $lastKey = array_key_last($node);
            }
            $node['__index'][$sig][] = &$node[$lastKey];

            return;
        }

        foreach ($node['__index'][$sig] as &$candidate) {
            foreach ($value as $vk => $vv) {
                if ($vk === '__index') {
                    continue;
                }
                if (!array_key_exists($vk, $candidate)) {
                    $candidate[$vk] = $vv;

                    continue;
                }
                if (is_array($candidate[$vk]) && is_array($vv)) {
                    $this->mergeNode($candidate[$vk], $vv);
                } else {
                    if (!is_array($candidate[$vk])) {
                        $candidate[$vk] = [$candidate[$vk]];
                    }
                    $candidate[$vk][] = $vv;
                }
            }
            unset($candidate);

            return;
        }

        $node[] = $value;
        $lastKey = array_key_last($node);
        $node['__index'][$sig][] = &$node[$lastKey];
    }

    private function signature(array $arr): string
    {
        $keys = [];
        foreach ($arr as $k => $_) {
            if ($k === '__index') {
                continue;
            }
            $keys[] = (string) $k;
        }
        sort($keys, SORT_STRING);

        return implode("\x1F", $keys);
    }

    private function isAssoc(array $arr): bool
    {
        $i = 0;
        foreach ($arr as $k => $_) {
            if ($k !== $i++) {
                return true;
            }
        }

        return false;
    }

    private function stripMeta(array &$node): void
    {
        if (!is_array($node)) {
            return;
        }
        unset($node['__index']);
        foreach ($node as &$v) {
            if (is_array($v)) {
                $this->stripMeta($v);
            }
        }
        unset($v);
    }

    private function detectDelimiterAndRewind($stream): string
    {
        $pos = ftell($stream);
        $line = fgets($stream);
        if ($line === false) {
            fseek($stream, $pos);

            return ',';
        }
        if (str_starts_with($line, "\xEF\xBB\xBF")) {
            $line = substr($line, 3);
        }
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
