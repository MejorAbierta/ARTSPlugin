<?php

namespace APP\plugins\generic\arts\traits;

use APP\facades\Repo;
use PKP\security\Role;

trait UserTrait {
    public function user($args, $request, $fromyaml = null)
    {
        $data = Repo::user()
            ->getCollector();

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterByUserIds([$args[0]]);
        }

        $data = $data->getMany();

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function reviewers($args, $request, $fromyaml = null)
    {
        $data = Repo::user()
            ->getCollector()
            ->filterByRoleIds([Role::ROLE_ID_REVIEWER]);

        if (is_string($args)) {
            $this->initFilter($args, $data);
        } else if (isset($args[0])) {
            $data->filterByUserIds([$args[0]]);
        }

        $data = $data->getMany();

        if ($fromyaml == null) {
            header('content-type: text/json');
            return json_encode($data);
        } else {
            return $data;
        }
    }


    function userGroup($args, $request, $fromyaml = null)
    {
        $data = Repo::userGroup()
            ->getCollector();

        if (isset($args[0])) {
            $data->filterByUserGroupIds([$args[0]]);
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