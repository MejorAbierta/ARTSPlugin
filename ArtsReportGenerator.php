<?php

namespace APP\plugins\generic\arts;

use Symfony\Component\Yaml\Yaml;
use APP\plugins\generic\arts\ArtsReportTemplate;

require_once __DIR__ . '/Utils.php';

class ArtsReportGenerator
{
    private $plugin;
    private $handler;

    public function __construct($plugin, $handler)
    {
        $this->plugin = $plugin;
        $this->handler = $handler;
    }

    public function generate($args, $request, $fromyaml = "yes")
    {
        if (sizeof($args) <= 0) {
            return;
        }

        $filePath = dirname(__FILE__, 1) . '/configs/' . $args[0] . '.yaml';

        try {
            $yaml = Yaml::parseFile($filePath);
        } catch (\Throwable $th) {
            return "Invalid yaml </br> $th";
        }

        $values = [];

        foreach ($yaml['report']['data'] as $data) {
            if ($data['output']['fields'] == null) {
                $fields = [];
            } else {
                $fields = explode(",", $data['output']['fields']);
                if (count($fields) == 0) {
                    $fields = [$data['output']['fields']];
                }
            }

            $this->handler->customfilters = [];
            try {
                $output = call_user_func([$this->handler, $data['operation']], $data['params'], $request, $fromyaml);
            } catch (\Throwable $th) {
                return $data['operation'] . "->" . $th->getMessage();
            }

            if (count($this->handler->customfilters) > 0) {
                foreach ($this->handler->customfilters as $key => $value) {
                    $output = $this->handler->filterBy($output, $value[0], $value[1]);
                }
            }
            if (count($fields) > 0) {
                $filtered = $this->filterFileds($output, $fields);
            } else {
                $filtered = json_decode(json_encode($output), true);
            }

            if (isset($data['output']['operation'])) {
                if ($data['output']['operation'] == 'count' && is_array($filtered)) {
                    $filtered[$data['output']['operation']] = count($filtered);
                }
            }
            if ($filtered != "No data") {

                switch ($yaml['report']['config']['format']) {
                    case 'csv':
                        $values[$data['id']] = json_to_csv_string(json_encode($filtered));
                        break;
                    default:
                        $values[$data['id']] = $filtered;
                        break;
                }
            }
        }
        $simpleName = str_replace(" ", "_", $yaml['report']['config']['name']);
        switch ($yaml['report']['config']['format']) {
            case 'json':
                header('content-type: text/json');
                return json_encode($values);
                break;
            case 'csv':
                download_csvs($values, $simpleName);
                break;
            case 'html':
                $form = new ArtsReportTemplate($this->plugin, $yaml['report']['config']['template']);
                $form->initData();
                if ($request->isPost($request)) {
                } else {
                    $form->display($request, $yaml['report']['config']['template'], $values);
                }
                break;
            case 'xml':
                $dom = new \DOMDocument();
                $root = $dom->createElement($simpleName);
                $dom->appendChild($root);
                arrayToXml($values, $root, $dom);
                $dom->formatOutput = true;
                header('Content-Type: application/xml; charset=UTF-8');
                return $dom->saveXML();

                break;

            default:

                break;
        }
    }

    function filterFileds($data, $fields)
    {
        if ($data == null) {
            return "No data";
        }

        if (is_string($data)) {
            return $data;
        }
        $result = [];

        foreach ($data as $key => $item) {
            $item = (array) $item;
            $row = [];

            if ($fields[0] == "") {
                if (isset($item['_data'])) {
                    $result[] = $item['_data'];
                } else {
                    $result[] = $item;
                }
            } else {
                foreach ($fields as $field) {
                    if (isset($item['_data'][$field])) {
                        $row[$field] = $item['_data'][$field];
                    } else if (isset($item[$field])) {
                        $row[$field] = $item[$field];
                    }
                }
                if (count($row) > 0) {
                    $result[] = $row;
                }
            }
        }
        if (count($result) == 1) {
            return $result[0];
        } else {
            return $result;
        }
    }
}