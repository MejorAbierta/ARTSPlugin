<?php

namespace APP\plugins\generic\arts\traits;

use APP\facades\Repo;

trait SectionTrait
{
    function section($args, $request, $fromyaml = null)
    {
        $contextId = $this->getJournalId();
        $data = Repo::section()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        }
        $data = $data->getMany();

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }
}
