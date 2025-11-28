<?php

namespace APP\plugins\generic\arts\traits;

use APP\facades\Repo;
use PKP\db\DAORegistry;
use APP\core\Application;

trait RepositoryTrait
{
    public function author($args, $request, $fromyaml = null)
    {
        $collector = Repo::author()->getCollector();
        return $this->processRepoRequest($collector, $args, $fromyaml);
    }

    public function category($args, $request, $fromyaml = null)
    {
        $collector = Repo::category()->getCollector();
        return $this->processRepoRequest($collector, $args, $fromyaml);
    }

    public function decision($args, $request, $fromyaml = null)
    {
        $collector = Repo::decision()->getCollector();
        return $this->processRepoRequest($collector, $args, $fromyaml);
    }

    public function institution($args, $request, $fromyaml = null)
    {

        $contextId = $this->getJournalId();
        $data = Repo::institution()
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

    function reviews($args, $request, $fromyaml = null)
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

        if (count($this->customfilters) > 0) {
            foreach ($this->customfilters as $key => $value) {
                $data = $this->filterBy($data, $value[0], $value[1]);
            }
            $this->customfilters = [];
        }

        $ids = [];
        foreach ($data as $obj) {
            $obj = (array)$obj;
            $ids[] = $obj['_data']['id'];
        }

        $data = [];

        foreach ($ids as $id) {
            $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
            $data[] = $reviewAssignmentDao->getBySubmissionId($id);
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function eventlogs($args, $request, $fromyaml = null)
    {
        if (!isset($args[0])) {
            error_log("submission id expected");
            return;
        }


        $contextId = $this->getJournalId();
        $data = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$contextId]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $ids = [$args[0]];
        }

        $data = $data->getMany();

        if (count($this->customfilters) > 0) {
            foreach ($this->customfilters as $key => $value) {
                $data = $this->filterBy($data, $value[0], $value[1]);
            }
            $this->customfilters = [];
        }

        $ids = [];
        foreach ($data as $obj) {
            $ids[] = $obj['_data']['id'];
        }

        $data = Repo::eventLog()
            ->getCollector()
            ->filterByAssoc(Application::ASSOC_TYPE_SUBMISSION, $ids)
            ->getMany();

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }
}
