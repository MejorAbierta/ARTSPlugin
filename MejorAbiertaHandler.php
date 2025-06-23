<?php

namespace APP\plugins\generic\mejorAbierta;

use APP\handler\Handler;

use PKP\handler\APIHandler;
use PKP\core\APIResponse;
use Slim\Http\Request as SlimRequest;

use APP\facades\Repo;
use PKP\security\Role;
use PKP\db\DAORegistry;
use PKP\security\authorization\ContextAccessPolicy;
use PKP\security\authorization\ContextRequiredPolicy;

use PKP\security\authorization\UserRolesRequiredPolicy;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;

use APP\security\authorization\OjsIssueRequiredPolicy;
use APP\security\authorization\OjsJournalMustPublishPolicy;

use PKP\security\authorization\PKPSiteAccessPolicy;


use APP\core\Application;


class MejorAbiertaHandler extends APIHandler
{
    var $bannedFields = ['password', 'apiKey', 'email', 'familyName', 'givenName'];


    public function __construct()
    {

        //PKPSiteAccessPolicy::SITE_ACCESS_ALL_ROLES
        $this->_handlerPath = 'mejorAbierta';

        //COOKIE auth
        $roles = [Role::ROLE_ID_AUTHOR];
        $this->_endpoints = [
            'GET' => [
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'getReviewers'],
                    //'roles' => $roles,
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'getJournalIdentity'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'announcement'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'author'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'category'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'citation'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'decision'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'institution'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'submissionFile'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'userGroup'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'representation'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'DAOs'],
                ],
                [
                    'pattern' => $this->getEndpointPattern(),
                    'handler' => [$this, 'DAO'],
                ],

                /*  [
                    'pattern' => $this->getEndpointPattern() . '/current',
                    'handler' => [$this, 'getCurrent'],
                    'roles' => $roles
                ],
                [
                    'pattern' => $this->getEndpointPattern() . '/{issueId:\d+}',
                    'handler' => [$this, 'get'],
                    'roles' => $roles
                ],*/
            ]
        ];
    }
    public function authorize($request, &$args, $roleAssignments)
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $this->read_env_file();
        $api_token = getenv('API_TOKEN');

        if ($token != "Bearer $api_token") {
            return $request->getDispatcher()->handle404();
        }
        return parent::authorize($request, $args, $roleAssignments);
    }

    function read_env_file()
    {
        $file_path = dirname(__FILE__, 1) . '/.env';
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


    /*

	function authorize($request, &$args, $roleAssignments) {
		$this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}
*//*
    public function authorize($request, &$args, $roleAssignments)
    {
        $apiKey = $_SERVER['Authorization'];
        echo $apiKey;
        if ($apiKey) {
            // Para API Key, solo verificamos que la key sea válida
            $this->addPolicy(new ApiKeyAuthorizationPolicy($request, null, $apiKey));
        } else {
            // Para acceso normal, verificamos roles de usuario
            $this->addPolicy(new UserRolesRequiredPolicy($request), true);

            $rolePolicy = new PolicySet(PolicySet::COMBINING_PERMIT_OVERRIDES);
            foreach ($roleAssignments as $role => $operations) {
                $rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
            }
            $this->addPolicy($rolePolicy);
        }
        return parent::authorize($request, $args, $roleAssignments);
    }*/

    public function announcement($args, $request)
    {

        $data = Repo::announcement()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }

    public function author($args, $request)
    {

        $data = Repo::author()
            ->getCollector()
            ->getMany();


        echo json_encode($this->anonimizeData($data));
    }

    public function category($args, $request)
    {

        $data = Repo::category()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }

    public function decision($args, $request)
    {

        $data = Repo::decision()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }

    public function institution($args, $request)
    {

        $data = Repo::institution()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }


    public function submissionFile($args, $request)
    {

        $data = Repo::submissionFile()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }

    public function userGroup($args, $request)
    {

        $data = Repo::userGroup()
            ->getCollector()
            ->getMany();


        echo json_encode($data);
    }

    function representation()
    {
        $contextId = Application::CONTEXT_JOURNAL;
        /** @var ContextDAO $contextDao */
        $representationDAO = Application::getRepresentationDAO();
        /** @var Context $context */
        $representation = $representationDAO->getById($contextId);

        echo json_encode($this->anonimizeData($representation));
    }


    public function getReviewers($args, $request)
    {

        $data = Repo::user()
            ->getCollector()
            ->filterByRoleIds([Role::ROLE_ID_REVIEWER])
            ->getMany();



        echo json_encode($this->anonimizeData($data));
    }





    function getJournalIdentity()
    {
        $contextId = Application::CONTEXT_JOURNAL;
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);

        /*
        $text = "Datos de la revista" . "|";
        $text .= "Nombre: " . implode(",", $context->getSetting('name')) . "|";
        $text .= "ISSN: " . $context->getSetting('printIssn') . "|";
        $text .= "ISSN electrónico: " . $context->getSetting('onlineIssn') . "|";
        $text .= "Entidad: " . $context->getSetting('publisherInstitution');
        */

        echo json_encode($this->anonimizeData($context));
    }

    function DAOs()
    {
        $data = DAORegistry::getDAOs();

        echo json_encode($data);
    }

    function DAO($args)
    {
        $data = DAORegistry::getDAO($args[0]);
        echo json_encode($data);
    }

    function anonimizeData($data)
    {


        if (get_class($data) === 'Illuminate\Support\LazyCollection') {
            return $data->map(function ($item) {

                $item = (array) $item;
                foreach ($this->bannedFields as $key => $value) {
                    unset($item['_data'][$value]);
                }

                return (object) $item;
            });
        } else {
            $collection = collect([$data]);

            return $collection->map(function ($item) {

                $item = (array) $item;
                foreach ($this->bannedFields as $key => $value) {
                    unset($item['_data'][$value]);
                }

                return (object) $item;
            });
        }
    }
}
