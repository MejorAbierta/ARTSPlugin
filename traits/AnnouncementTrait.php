<?php

namespace APP\plugins\generic\arts\traits;

use APP\facades\Repo;

trait AnnouncementTrait {
    public function announcement($args, $request, $fromyaml = null)
    {

        $data = Repo::announcement()
            ->getCollector();

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