<?php

namespace APP\plugins\generic\arts\traits;

use APP\core\Application;
use PKP\db\DAORegistry;
use Illuminate\Support\Facades\DB;

trait UtilityTrait
{
    function getJournalId()
    {
        $context = Application::get()
            ->getRequest()
            ->getContext();

        return $context->getId();
    }

    function read_env_file()
    {
        $file_path = dirname(__FILE__, 2) . '/.env';
        if (!file_exists($file_path)) {
            return;
        }

        $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv("$key=$value");
            }
        }
    }


    function downloadFile($filePath, $fileName)
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            echo "File not found.";
            return;
        }

        // Get the file size
        $fileSize = filesize($filePath);

        // Get the file type
        $fileType = mime_content_type($filePath);

        // Set the headers
        header('Content-Type: ' . $fileType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);

        // Read the file and output it
        readfile($filePath);
    }

    function anonimizeData($data)
    {

        if (get_class($data) != 'Illuminate\Support\LazyCollection') {
            $data = collect([$data]);
        }

        return $data->map(function ($item) {

            $item = (array) $item;
            foreach ($this->bannedFields as $key => $value) {
                if (!isset($item['_data'])) {
                    break;
                }
                unset($item['_data'][$value]);

                if (isset($item['_data']['publications'])) {
                    $item['_data']['publications'] = $this->anonimizeData($item['_data']['publications']);
                }

                if (isset($item['_data']['authors'])) {
                    $item['_data']['authors'] = $this->anonimizeData($item['_data']['authors']);
                }
            }

            return (object) $item;
        });
    }

    function DAO($args, $request, $fromyaml = null)
    {
        if (isset($args[0])) {
            $data = DAORegistry::getDAO($args[0]);
        } else {
            $data = DAORegistry::getDAOs();
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    function doQuery($args, $request, $fromyaml = null)
    {
        try {
            $sql = $args;

            if (!isPureSelect($sql)) {
                return "OPERATION NOT PERMITED " . $sql;
            }

            $result = DB::cursor(DB::raw($sql)->getValue(DB::connection()->getQueryGrammar()), []);

            if ($result) {
                $rows = [];
                while ($row = $result->current()) {
                    $rows[] = $row;
                    $result->next();
                }
                if (count($rows) == 1) {
                    return $rows[0];
                } else {
                    return $rows;
                }
            } else {
                return json_encode([]);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    private function processRepoRequest($collector, $args, $fromyaml = null)
    {
        if (is_string($args)) {
            $this->initFilter($args, $collector);
        }

        $data = $collector->getMany();

        if ($fromyaml == null) {
            header('content-type: text/json');
            return json_encode($data);
        } else {
            return $data;
        }
    }
}
