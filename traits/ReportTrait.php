<?php

namespace APP\plugins\generic\arts\traits;

use APP\plugins\generic\arts\ArtsReportForm;
use APP\plugins\generic\arts\ArtsReportGenerator;
use Symfony\Component\Yaml\Yaml;

trait ReportTrait
{
    function parseyaml($args, $request, $fromyaml = "yes")
    {
        $reportGenerator = new ArtsReportGenerator($this->plugin, $this);
        return $reportGenerator->generate($args, $request, $fromyaml);
    }

    function report($args, $request)
    {

        $form = new ArtsReportForm($this->plugin);
        $form->initData();
        if ($request->isPost($request)) {
            $reportParams = $request->getUserVars();

            $filename =  $reportParams['titlefile'];

            if ($filename == "") {
                return "Invalid title file";
            }
            $content = $reportParams['textyaml'];
            try {
                $yaml = Yaml::parse($content);
            } catch (\Throwable $th) {
                return "Invalid yaml </br> $th";
            }

            if ($content == "") {
                return "Invalid yaml";
            }

            $filePath = dirname(__FILE__, 2) . '/configs/' . $reportParams['titlefile'] . '.yaml';

            $content = $reportParams['textyaml'];


            if ($fp = fopen($filePath, 'w')) {
                fwrite($fp, $content);
                fclose($fp);
                echo "File created successfully.";
            } else {
                error_log("Error creating file.");
            }
        } else {
            $dateStart = date('Y-01-01');
            $dateEnd = date('Y-m-d');
            $form->display($request, 'list.tpl', [$dateStart, $dateEnd]);
        }
    }
}
