<?php


function json_to_csv_string(string|array $jsonInput, string $delimiter = ',', string $enclosure = '"', string $escape = "\\"): string
{
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


function arrayToXml($array, $parentElement, $dom)
{
    foreach ($array as $key => $value) {

        try {
            if (is_array($value) || is_object($value)) {
                if (is_numeric($key)) {
                    $child = $dom->createElement('item');
                } else {
                    $child = $dom->createElement($key);
                }
                $parentElement->appendChild($child);
                arrayToXml($value, $child, $dom);
            } else {
                if (is_numeric($key)) {
                    $child = $dom->createElement('item', $value);
                } else if (is_string($value)) {
                    $child = $dom->createElement($key, $value);
                } else if (is_bool($value)) {
                    $child = $dom->createElement($key, $value ? 'true' : 'false');
                } else {
                    $child = $dom->createElement($key, $value);
                }
                $parentElement->appendChild($child);
            }
        } catch (\Throwable $th) {
            error_log("ERROR PARSING TO XML {$key} {$value}");
        }
    }
}



function download_csvs($files, $filename)
{
    $zip = new ZipArchive();
    $zip->open('csvs.zip', ZipArchive::CREATE);

    foreach ($files as $key => $value) {
        $zip->addFromString($filename . "_" . $key . '.csv', $value);
    }

    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=' . $filename . '.zip');
    header('Content-Length: ' . filesize('csvs.zip'));
    readfile('csvs.zip');
    unlink('csvs.zip');
}
