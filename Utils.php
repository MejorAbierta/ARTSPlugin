<?php


function json_to_csv_string(string|array $jsonInput, string $delimiter = ',', string $enclosure = '"', string $escape = "\\"): string {
    $data = is_string($jsonInput) ? json_decode($jsonInput, true) : $jsonInput;
    if ($data === null || $data === false) return '';

    if (array_keys($data) !== range(0, count($data) - 1)) {
        if (!empty($data) && is_array(current($data)) && array_keys($data) === range(0, count($data) - 1)) {
        } else {
           
            $data = [$data];
        }
    }

    $headers = [];
    foreach ($data as $row) {
        if (is_array($row)) {
            foreach ($row as $k => $_) {
                if (!in_array($k, $headers, true)) $headers[] = $k;
            }
        }
    }

    $fp = fopen('php://temp', 'r+');

    if (!empty($headers)) {
        fputcsv($fp, $headers, $delimiter, $enclosure, $escape);
    }

    foreach ($data as $row) {
        $line = [];
        foreach ($headers as $h) {
            $val = $row[$h] ?? '';
            if (is_array($val) || is_object($val)) $val = json_encode($val, JSON_UNESCAPED_UNICODE);
            $line[] = $val;
        }
        fputcsv($fp, $line, $delimiter, $enclosure, $escape);
    }

    rewind($fp);
    $csv = stream_get_contents($fp);
    fclose($fp);
    return $csv;
}