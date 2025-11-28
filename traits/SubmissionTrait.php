<?php

namespace APP\plugins\generic\arts\traits;

use APP\facades\Repo;
use PKP\submission\PKPSubmission;

trait SubmissionTrait
{

    function submissions($args, $request, $fromyaml = null)
    {
        $contextId = $this->getJournalId();
        $data = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterByIssueIds([$args[0]]);
        }

        $data = $data->getMany();
        /*
        $filteredElements = $submissions->filter(function ($element) {
            return $element->getData("dateSubmitted") >= date("", 1741873765);
        });
        */

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function publications($args, $request, $fromyaml = null)
    {
        $contextId = $this->getJournalId();
        $data = Repo::publication()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterBySubmissionIds([$args[0]]);
        }

        $data = $data->getMany();


        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    function galley($args, $request, $fromyaml = null)
    {
        $contextId = $this->getJournalId();
        $data = Repo::galley()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterByPublicationIds([$args[0]]);
        }

        $data = $data->getMany();


        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function submissionFile($args, $request, $fromyaml = null)
    {
        $collector = Repo::submissionFile()->getCollector();
        return $this->processRepoRequest($collector, $args, $fromyaml);
    }

    function submissionFileDownload($args, $request, $fromyaml = null)
    {

        $data = Repo::submissionFile()
            ->getCollector();

        if (is_string($args)) {
            $this->initFilter($args, $data);
        }

        $data = $data->getMany();

        $files = [];
        foreach ($data as $key => $value) {
            $path = \Config::getVar('files', 'files_dir') . '/' . $value->getData('path');
            $fileName = $value->getLocalizedData('name');
            if (file_exists($path)) {

                $files[] = [
                    'path' => $path,
                    'name' => $fileName,
                ];
            }
        }

        if (count($files) > 0) {
            $this->compressAndDownload($files, "submissionFiles.zip");
        }
    }

    function compressAndDownload($files, $zipFileName)
    {
        // Create a new zip file
        $zip = new \ZipArchive();
        $tempZipFile = tempnam(sys_get_temp_dir(), 'zip');
        if ($zip->open($tempZipFile, \ZipArchive::CREATE) === TRUE) {
            // Add files to the zip
            foreach ($files as $file) {
                $zip->addFile($file['path'], $file['name']);
            }
            // Close the zip file
            $zip->close();

            // Output the zip file
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
            header('Content-Length: ' . filesize($tempZipFile));
            ob_clean();
            flush();
            readfile($tempZipFile);
            unlink($tempZipFile);
            exit;
        } else {
            echo "Error creating zip file.";
        }
    }
}
