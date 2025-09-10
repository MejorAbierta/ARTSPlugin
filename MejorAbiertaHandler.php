<?php

namespace APP\plugins\generic\mejorAbierta;

use APP\handler\Handler;

use PKP\handler\APIHandler;
use PKP\core\APIResponse;
use Slim\Http\Request as SlimRequest;

use APP\facades\Repo;
use PKP\security\Role;
use PKP\db\DAORegistry;
use PKP\submission\PKPSubmission;
use PKP\security\authorization\ContextAccessPolicy;
use PKP\security\authorization\ContextRequiredPolicy;

use PKP\security\authorization\UserRolesRequiredPolicy;
use PKP\security\authorization\PolicySet;
use PKP\security\authorization\RoleBasedHandlerOperationPolicy;

use APP\security\authorization\OjsIssueRequiredPolicy;
use APP\security\authorization\OjsJournalMustPublishPolicy;

use PKP\security\authorization\PKPSiteAccessPolicy;

use Symfony\Component\Yaml\Yaml;
use APP\core\Application;


class MejorAbiertaHandler extends APIHandler
{
    var $bannedFields = [
        'password',
        'apiKey',
        'email',
        // 'familyName',
        // 'givenName',
        'accessStatus'
    ];

    private $plugin;


    public function __construct($plugin)
    {

        $this->plugin = $plugin;

        //PKPSiteAccessPolicy::SITE_ACCESS_ALL_ROLES
        $this->_handlerPath = 'mejorAbierta';

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {

            $this->addRoleAssignment(
                [
                    Role::ROLE_ID_MANAGER,
                    Role::ROLE_ID_SITE_ADMIN,
                ],
                [
                    'author',
                    'about',
                    'reviewers',
                    'journalIdentity',
                    'announcement',
                    'category',
                    'citation',
                    'decision',
                    'institution',
                    'submissionFile',
                    //'userGroup',
                    'representation',
                    'submissions',
                    'issues',
                    'urls',
                    'reviews',
                    'eventlogs',
                    'parseyaml',
                    'report',
                    'DAO',
                    'user'
                ]
            );
        }
    }
    public function authorize($request, &$args, $roleAssignments)
    {
        $headers = getallheaders();
        $this->read_env_file();
        $api_token = getenv('API_TOKEN');

        if (isset($headers['Authorization'])) {
            if ($headers['Authorization'] != "Bearer $api_token") {
                return $request->getDispatcher()->handle404();
            }
        } else {
            $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
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


        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function author($args, $request)
    {

        $data = Repo::author()
            ->getCollector()
            ->getMany();


        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    public function user($args, $request)
    {
        $data = Repo::user()
            ->getCollector()
            ->getMany();


        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function decision($args, $request)
    {

        $data = Repo::decision()
            ->getCollector()
            ->getMany();


        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function institution($args, $request)
    {

        $contextId = Application::CONTEXT_JOURNAL;
        $data = Repo::institution()
            ->getCollector()
            ->filterByContextIds([$contextId])
            ->getMany();


        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    function submissionFile($args, $request)
    {

        $data = Repo::submissionFile()
            ->getCollector();

        if (isset($args[0])) {
            $data->filterBySubmissionIds([$args[0]]);
        }

        $data = $data->getMany();
        //echo json_encode($data);

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

    function downloadFile($filePath, $fileName)
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            echo "File not found.";
            return;
        }

        // Get the file size
        $fileSize = filesize($filePath);

        // Get the file type
        $fileType = mime_content_type($filePath);

        // Set the headers
        header('Content-Type: ' . $fileType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);

        // Read the file and output it
        readfile($filePath);
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


    public function reviewers($args, $request)
    {

        $data = Repo::user()
            ->getCollector()
            ->filterByRoleIds([Role::ROLE_ID_REVIEWER]);



        if (isset($args[0])) {
            $data->filterByUserIds([$args[0]]);
        }

        $data = $data->getMany();


        if ($request != null) {
            header('content-type: text/json');
            return json_encode($data);
        } else {
            return $data;
        }
    }

    function filter($s, $symbol)
    {
        $parts = explode($symbol, $s);
        if (count($parts) > 1) {
            echo 'The string contains a comma.';
        } else {
            echo 'The string does not contain a comma.';
            $parts = explode('=', $args);
        }
    }




    function journalIdentity()
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
        $context->printIssn = $context->getSetting('printIssn');
        $context->onlineIssn = $context->getSetting('onlineIssn');
        $context->publisherInstitution = $context->getSetting('publisherInstitution');

        echo json_encode($this->anonimizeData($context));
    }


    function DAO($args, $request)
    {
        if (isset($args[0])) {
            $data = DAORegistry::getDAO($args[0]);
        } else {
            $data = DAORegistry::getDAOs();
        }

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function about($args, $request): string
    {
        $contextId = Application::CONTEXT_JOURNAL;
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);
        return implode(",", $context->getData('about'));
    }


    function submissions($args, $request)
    {
        /*
        public const STATUS_QUEUED = 1;
        public const STATUS_PUBLISHED = 3;
        public const STATUS_DECLINED = 4;
        public const STATUS_SCHEDULED = 5;
        */

        $contextId = Application::CONTEXT_JOURNAL;
        $data = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$contextId]);
        //->filterByStatus($status)

        if (isset($args[0])) {
            $data->filterByIssueIds([$args[0]]);
        }
        $data = $data->getMany();
        /*
        $filteredElements = $submissions->filter(function ($element) {
            return $element->getData("dateSubmitted") >= date("", 1741873765);
        });
        */

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function issues($args, $request)
    {

        $contextId = Application::CONTEXT_JOURNAL;
        $data = Repo::issue()
            ->getCollector()
            ->filterByContextIds([$contextId]);
        //  ->filterByPublished(true);
        //  ->filterByStatus($status)

        //is string when recived from yaml
        if (is_string($args)) {
            echo "params --->" . $args;
            $parts = explode(';', $args);
            if (count($parts) > 1) {
                echo 'The string contains a comma.';
            } else {
                echo 'The string does not contain a comma.';
                $parts = explode('=', $args);
                if (count($parts) > 1) {
                    $fun = $parts[0];
                    $param = $parts[1];
                    $paramparts = explode(',', $param);

                    if ($fun == "year") {
                        # code...
                        if (count($parts) > 1) {
                            $data->filterByYears([$param]);
                        } else {
                            $data->filterByYears($paramparts);
                        }
                    }
                } else {
                    $parts = explode('>', $args);
                }
            }
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

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
        //echo json_encode($this->anonimizeData($data));
    }

    function section($args, $request)
    {
        $contextId = Application::CONTEXT_JOURNAL;
        $data = Repo::section()
            ->getCollector()
            ->filterByContextIds([$contextId])
            ->getMany();

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function urls($args, $request)
    {
        $router = $request->getRouter();
        $dispatcher = $router->getDispatcher();
        $data = [];
        $data["home"] = $dispatcher->url($request, ROUTE_PAGE, null, 'index', null, null);
        $data["editorialTeam"] = $dispatcher->url($request, ROUTE_PAGE, null, 'about', 'editorialTeam');
        $data["submissions"] = $dispatcher->url($request, ROUTE_PAGE, null, 'about', 'submissions');
        $data["about"] = $dispatcher->url($request, ROUTE_PAGE, null, 'about');
        $data["privacy"] = $dispatcher->url($request, ROUTE_PAGE, null, 'about', 'privacy');
        $data["contact"] = $dispatcher->url($request, ROUTE_PAGE, null, 'about', 'contact');

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function reviews($args, $request)
    {
        //this seems to return empty...?
        $submissionId = $args[0];

        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $data = $reviewAssignmentDao->getBySubmissionId($submissionId);

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function eventlogs($args, $request)
    {
        if (!isset($args[0])) {
            error_log("submission id expected");
            return;
        }

        $submissionId = $args[0];

        $data = Repo::eventLog()
            ->getCollector()
            ->filterByAssoc(Application::ASSOC_TYPE_SUBMISSION, [$submissionId])
            ->getMany();

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    function anonimizeData($data)
    {

        if (get_class($data) != 'Illuminate\Support\LazyCollection') {
            $data = collect([$data]);
        }

        return $data->map(function ($item) {

            $item = (array) $item;
            foreach ($this->bannedFields as $key => $value) {
                if (!isset($item['_data'])) {
                    break;
                }
                unset($item['_data'][$value]);

                if (isset($item['_data']['publications'])) {
                    $item['_data']['publications'] = $this->anonimizeData($item['_data']['publications']);
                }

                if (isset($item['_data']['authors'])) {
                    $item['_data']['authors'] = $this->anonimizeData($item['_data']['authors']);
                }
            }

            return (object) $item;
        });
    }

    function parseyaml($args)
    {
        if (sizeof($args) <= 0) {
            return;
        }
        $filePath = dirname(__FILE__, 1) . '/configs/' . $args[0] . '.yaml';



        $yaml = Yaml::parseFile($filePath);

        //print_r($yaml);
        echo "ID de la configuración: " . $yaml['report']['config']['id'] . "\n";
        echo "Nombre de la configuración: " . $yaml['report']['config']['name']     . "\n";

        foreach ($yaml['report']['data'] as $data) {
            echo "description: " . $data['title'] . "</br>";
            echo "operation: " . $data['operation'] . "</br>";
            echo "params: " . $data['params'] . "</br>";
            $output = call_user_func([$this, $data['operation']], $data['params'], "");
            echo $data['output']['fields'];
            echo $output;
        }
    }

    function userGroup($args, $request)
    {
        $data = Repo::userGroup()
            ->getCollector();

        if (isset($args[0])) {
            $data->filterByUserGroupIds([$args[0]]);
        }

        $data = $data->getMany();

        if ($request != null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function report($args, $request)
    {

        $form = new MejorAbiertaReportForm($this->plugin);
        $form->initData();
        if ($request->isPost($request)) {
            $reportParams = $request->getUserVars();

            $filePath = dirname(__FILE__, 1) . '/configs/' . $reportParams['titlefile'] . '.yaml';

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
            $form->display($request, 'form.tpl', [$dateStart, $dateEnd]);
        }
    }
}
