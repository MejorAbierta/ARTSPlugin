<?php

namespace APP\plugins\generic\arts\traits;

use APP\core\Application;
use APP\facades\Repo;
use PKP\submission\PKPSubmission;

trait IssuesTrait
{
    function issues($args, $request, $fromyaml = null)
    {

        $contextId = $this->getJournalId();
        $data = Repo::issue()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        //is string when recived from yaml
        if (is_string($args)) {

            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterByIssueIds([$args[0]]);
        }
        $data = $data->getMany();


        $result = [];
        foreach ($data as $key => $item) {
            //echo json_encode($item->_data["id"]);
            $item->_data["submissions"] = Repo::submission()
                ->getCollector()
                ->filterByContextIds([$contextId])
                ->filterByIssueIds([$item->_data["id"]])
                ->filterByStatus([PKPSubmission::STATUS_PUBLISHED])
                ->orderBy('seq', 'ASC')
                ->getMany();

            $result[] = $item;
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            return json_encode($data);
        } else {
            return $data;
        }
    }

}

