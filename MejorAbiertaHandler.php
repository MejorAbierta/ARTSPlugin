<?php

namespace APP\plugins\generic\mejorAbierta;

use PKP\handler\APIHandler;

use APP\facades\Repo;
use PKP\security\Role;
use PKP\db\DAORegistry;
use PKP\submission\PKPSubmission;
use PKP\security\authorization\ContextAccessPolicy;

use Symfony\Component\Yaml\Yaml;
use APP\core\Application;

require_once __DIR__ . '/Utils.php';

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
    var $customfilters = [];

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

    public function announcement($args, $request, $fromyaml)
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

    public function author($args, $request, $fromyaml)
    {

        $data = Repo::author()
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


    public function user($args, $request, $fromyaml)
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

    public function decision($args, $request, $fromyaml)
    {

        $data = Repo::decision()
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

    public function institution($args, $request, $fromyaml)
    {

        $contextId = Application::CONTEXT_JOURNAL;
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


    function submissionFile($args, $request, $fromyaml)
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


    public function reviewers($args, $request, $fromyaml)
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





    function journalIdentity($args, $request, $fromyaml)
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

        if (is_string($args)) {
            $this->initFilter($args, $context);
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($context);
        } else {
            return $context;
        }
    }


    function DAO($args, $request, $fromyaml)
    {
        if (isset($args[0])) {
            $data = DAORegistry::getDAO($args[0]);
        } else {
            $data = DAORegistry::getDAOs();
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function about($args, $request, $fromyaml)
    {



        $contextId = Application::CONTEXT_JOURNAL;
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);
        $data = $context->getData('about');

        if (is_string($args)) {
            $data = $context->getData($args);
        } else if (isset($args[0])) {
            $data = $context->getData($args[0]);
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }


    function submissions($args, $request, $fromyaml)
    {

        $contextId = Application::CONTEXT_JOURNAL;
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

    function filter($fun, $param, $data)
    {
        $paramparts = explode(',', $param);

        if (count($paramparts) > 1) {
            $paramsFormated = [$param];
        } else {
            $paramsFormated = $paramparts;
        }
        switch ($fun) {
            case 'year':                                        //issues
                $data->filterByYears($paramsFormated);
                break;
            case 'published':                                   //issues
                $data->filterByPublished($param == "true");
                break;
            case 'hasdois':                                     //issues,submissions
                $data->filterByHasDois($param == "true");
                break;
            case 'volumes':                                     //issues 
                $data->filterByVolumes($paramsFormated);
                break;
            case 'titles':                                      //issues,section
                $data->filterByTitles($paramsFormated);
                break;
            case 'numbers':                                     //issues
                $data->filterByNumbers($paramsFormated);
                break;
            case 'status':                                      //submissions,user                                     
                $data->filterByStatus($paramsFormated);
                break;
            case 'doistatuses':                                 //issues,submissions
                $data->filterByDoiStatuses($paramsFormated);
                break;
            case 'issueids':                                    //issues,submissions
                $data->filterByIssueIds($paramsFormated);
                break;
            case 'urlpath':                                     //issues
                $data->filterByUrlPath($param);
                break;
            case 'categoryid':                                  //submissions
                $data->filterByCategoryIds($paramsFormated);
                break;
            case 'daysinactive':                                //submissions
                $data->filterByDaysInactive(intval($param));
                break;
            case 'incomplete':                                  //submissions
                $data->filterByIncomplete($param == "true");
                break;
            case 'overdue':                                     //submissions
                $data->filterByOverdue($param == "true");
                break;
            case 'sectionids':                                  //submissions
                $data->filterBySectionIds($paramsFormated);
                break;
            case 'stageids':                                    //submissions,decision
                $data->filterByStageIds($paramsFormated);
                break;
            case 'averagecompletion':                           //user
                $data->filterByAverageCompletion(intval($param));
                break;
            case 'dayssincelastassignment':                     //user
                $data->filterByDaysSinceLastAssignment(intval($param));
                break;
            case 'reviewerrating':                              //user
                $data->filterByReviewerRating(intval($param));
                break;
            case 'reviewsactive':                               //user
                $data->filterByReviewsActive(intval($param));
                break;
            case 'reviewscompleted':                            //user
                $data->filterByReviewsCompleted(intval($param));
                break;
            case 'roleids':                                     //user
                $data->filterByRoleIds($paramsFormated);
                break;
            case 'settings':                                    //user
                $data->filterBySettings($paramsFormated);
                break;
            case 'workflowstageids':                            //user
                $data->filterByWorkflowStageIds($paramsFormated);
                break;
            case 'ips':                                         //institutions
                $data->filterByIps($paramsFormated);
                break;
            case 'decisiontypes':                               //decision
                $data->filterByDecisionTypes($paramsFormated);
                break;
            case 'editorids':                                   //decision 
                $data->filterByEditorIds($paramsFormated);
                break;
            case 'reviewroundids':                              //decision
                $data->filterByReviewRoundIds($paramsFormated);
                break;
            case 'rounds':                                      //decision
                $data->filterByRounds($paramsFormated);
                break;
            case 'submissionids':                               //decision
                $data->filterBySubmissionIds($paramsFormated);
                break;
            case 'affiliation':                                 //author
                $data->filterByAffiliation($paramsFormated);
                break;
            case 'country':                                     //author
                $data->filterByCountry($paramsFormated);
                break;
            case 'includeinbrowse':                             //author
                $data->filterByIncludeInBrowse($param == "true");
                break;
            case 'name':                                        //author
                $data->filterByName($param);
                break;
            case 'publicationids':                              //author
                $data->filterByPublicationIds($paramsFormated);
                break;
            case 'active': //YYYY-MM-DD                         //announcement 
                $data->filterByActive($param);
                break;
            case 'typeids':                                     //announcement
                $data->filterByTypeIds($paramsFormated);
                break;
            case 'abbrevs':                                     //section
                $data->filterByAbbrevs($paramsFormated);
                break;
            case 'contextid':
                $data->filterByContextIds($paramsFormated);
                break;
            default:
                break;
        }
    }

    function filterBy($data, $filtername, $value)
    {
        if ($data == null) {
            return "No data";
        }

        if (is_string($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $item) {
            $item = (array) $item;
            if (isset($item['_data'][$filtername]['en'])) {
                if (strtolower($item['_data'][$filtername]['en']) == strtolower($value)) {
                    $result[] = $item;
                }
            } else if (isset($item['_data'][$filtername])) {
                if (strtolower($item['_data'][$filtername]) == strtolower($value)) {
                    $result[] = $item;
                }
            }
        }

        return $result;
    }

    function initFilter($args, $data)
    {
        $this->customfilters = [];
        try {
            $parts = explode(';', $args);
            if (count($parts) > 1) {
                foreach ($parts as $key => $value) {
                    $parts = explode('=', $value);
                    if (count($parts) > 1) {
                        $fun = $parts[0];
                        $param = $parts[1];

                        try {
                            $this->filter($fun, $param, $data);
                        } catch (\Throwable $th) {
                            $this->customfilters[] = [$fun, $param];
                        }
                    } else {
                        //$parts = explode('>', $args);
                    }
                }
            } else {
                $parts = explode('=', $args);
                if (count($parts) > 1) {
                    $fun = $parts[0];
                    $param = $parts[1];
                    try {
                        $this->filter($fun, $param, $data);
                    } catch (\Throwable $th) {
                        $this->customfilters[] = [$fun, $param];
                    }
                } else {
                    //$parts = explode('>', $args);
                }
            }
        } catch (\Throwable $th) {
            echo (json_encode($th->getMessage()));
            error_log("Error in filter: " . $th->getMessage());
        }
    }


    function issues($args, $request, $fromyaml)
    {

        $contextId = Application::CONTEXT_JOURNAL;
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
            echo json_encode($data);
        } else {
            return $data;
        }
        //echo json_encode($this->anonimizeData($data));
    }

    function section($args, $request, $fromyaml)
    {
        $contextId = Application::CONTEXT_JOURNAL;
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

    function urls($args, $request, $fromyaml)
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

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function reviews($args, $request, $fromyaml)
    {
        //this seems to return empty...?
        $submissionId = $args[0];

        $reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
        $data = $reviewAssignmentDao->getBySubmissionId($submissionId);

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    function eventlogs($args, $request, $fromyaml)
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

        if ($fromyaml == null) {
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

    function filterFileds($data, $fields)
    {
        if ($data == null) {
            return "No data";
        }

        if (is_string($data)) {
            return $data;
        }/*
        if (get_class($data) != 'Illuminate\Support\LazyCollection') {
            $data = collect([$data]);
        }*/
        $result = [];




        foreach ($data as $key => $item) {
            $item = (array) $item;
            $row = [];

            if ($fields[0] == "") {
                if (isset($item['_data'])) {
                    $result[] = $item['_data'];
                } else {
                    $result[] = $item;
                }
            } else {
                foreach ($fields as $field) {
                    if (isset($item['_data'][$field])) {
                        $row[$field] = $item['_data'][$field];
                    } else if (isset($item[$field])) {
                        $row[$field] = $item[$field];
                    }
                }
                $result[] = $row;
            }
        }

        // $result now contains only the selected fields
        return $result;
    }



    function parseyaml($args, $request, $fromyaml = "yes")
    {

        if (sizeof($args) <= 0) {
            return;
        }
        
        $filePath = dirname(__FILE__, 1) . '/configs/' . $args[0] . '.yaml';

        try {
            $yaml = Yaml::parseFile($filePath);
        } catch (\Throwable $th) {
            return "Invalid yaml </br> $th";
        }

        $values = [];

        //print_r($yaml);
        //echo "ID: " . $yaml['report']['config']['id'] . "</br>";
        //echo "Name: " . $yaml['report']['config']['name']     . "</br>";

        foreach ($yaml['report']['data'] as $data) {
            //echo "Description: </br>" . str_replace("\n", "</br>", $data['description']) . "</br>";
            //echo "Operation: " . $data['operation'] . "</br>";
            //echo "Filter: " . $data['params'] . "</br>";
            //echo "Fields: " . $data['output']['fields'] . "</br>";
            $fields = explode(",", $data['output']['fields']);
            if (count($fields) == 0) {
                $fields = [$data['output']['fields']];
            }

            $this->customfilters = [];

            $output = call_user_func([$this, $data['operation']], $data['params'], $request, $fromyaml);

            if (count($this->customfilters) > 0) {
                foreach ($this->customfilters as $key => $value) {
                    $output = $this->filterBy($output, $value[0], $value[1]);
                }
            }

            $filtered = $this->filterFileds($output, $fields);
            //echo $data['output']['fields'];
            if ($yaml['report']['config']['format'] == 'json') {
                header('content-type: text/json');
                echo json_encode($filtered);
            }

            if ($yaml['report']['config']['format'] == 'csv') {
                $csv = json_to_csv_string(json_encode($filtered));
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="export.csv"');
                echo $csv;
            }

            if ($yaml['report']['config']['format'] == 'html') {
                $form = new MejorAbiertaReportTemplate($this->plugin,$yaml['report']['config']['template']);
                $form->initData();
                if ($request->isPost($request)) {
  
                } else {
                    $form->display($request, $yaml['report']['config']['template'], [$filtered]);
                }
            }

            if (isset($data['output']['operation']))
                if ($data['output']['operation'] == 'count') {
                    echo "</br>Operation: " . $data['output']['operation'] . ":";
                    echo count($output);
                    $values[$data['id']] = count($output);
                }

            //echo "</br>-------------------</br>";
        }
    }

    function userGroup($args, $request, $fromyaml)
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

    function report($args, $request)
    {

        $form = new MejorAbiertaReportForm($this->plugin);
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
