<?php

namespace APP\plugins\generic\mejorAbierta;

use APP\handler\Handler;


use APP\facades\Repo;
use PKP\security\Role;

class MejorAbiertaHandler extends Handler
{

    public function hola($args, $request)
    {
        echo "hola x" . json_encode($args);
    }
    public function reviewers($args, $request)
    {
        
        $data = Repo::user()
                ->getCollector()
                ->filterByRoleIds([Role::ROLE_ID_REVIEWER])
                ->getMany();
        
        foreach ($data as $key => $value) {
           $value->setData("password","none");
           echo $value->getData("password");
          //echo $data->getData($key);
        }
        echo json_encode($data);
    }
}
