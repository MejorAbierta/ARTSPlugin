<?php


namespace APP\plugins\generic\mejorAbierta;

use APP\core\Application;
use APP\submission\Submission;
use APP\facades\Repo;
use APP\template\TemplateManager;
use PKP\core\PKPString;
use PKP\facades\Locale;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use PKP\security\Role;
use PKP\submission\SubmissionComment;

class ReportForm extends Form
{
    /* @var int Associated context ID */
    private $contextId;

    /* @var ReviewersReport  */
    private $plugin;

    private $application;
    private $sections;
    private $submissionDateInterval;
    private $finalDecisionDateInterval;
    private $includeViews;

    /**
     * Constructor
     *
     * @param $plugin ReviewersReport Manual payment plugin
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->application = substr(Application::getName(), 0, 3);
        $request = Application::get()->getRequest();
        $this->contextId = $request->getContext()->getId();
        $this->sections = [];
        $this->submissionDateInterval = null;
        $this->finalDecisionDateInterval = null;
        $this->includeViews = false;

        parent::__construct($plugin->getTemplateResource('example.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Initialize form data.
     */
    public function initData()
    {
        $contextId = $this->contextId;
        $plugin = $this->plugin;
        $this->setData('mejorAbierta', $plugin->getSetting($contextId, 'mejorAbierta'));
    }

    public function validateReportData($reportParams)
    {
        if (array_key_exists('sections', $reportParams)) {
            $this->sections = $reportParams['sections'];
        }
        if (array_key_exists('includeViews', $reportParams)) {
            $this->includeViews = $reportParams['includeViews'];
        }
        $filteringType = $reportParams['selectFilterTypeDate'];

        if ($filteringType == 'filterBySubmission' || $filteringType == 'filterByBoth') {
            $submissionDateInterval = $this->validateDateInterval($reportParams['startSubmissionDateInterval'], $reportParams['endSubmissionDateInterval'], 'plugins.reports.scieloSubmissionsReport.warning.errorSubmittedDate');
            if (is_null($submissionDateInterval)) {
                return false;
            }
            $this->submissionDateInterval = $submissionDateInterval;
        }

        if ($filteringType == 'filterByFinalDecision' || $filteringType == 'filterByBoth') {
            $finalDecisionDateInterval = $this->validateDateInterval($reportParams['startFinalDecisionDateInterval'], $reportParams['endFinalDecisionDateInterval'], 'plugins.reports.scieloSubmissionsReport.warning.errorDecisionDate');
            if (is_null($finalDecisionDateInterval)) {
                return false;
            }
            $this->finalDecisionDateInterval = $finalDecisionDateInterval;
        }

        return true;
    }

    private function validateDateInterval($startInterval, $endInterval, $errorMessage)
    {
        /*
        $dateInterval = new ClosedDateInterval($startInterval, $endInterval);
        if (!$dateInterval->isValid()) {
            echo __($errorMessage);
            return null;
        }*/
        return 0;
    }

    private function emitHttpHeaders($request)
    {
        $context = $request->getContext();
        header('content-type: text/comma-separated-values');
        $acronym = PKPString::regexp_replace('/[^A-Za-z0-9 ]/', '', $context->getLocalizedAcronym());
        header('content-disposition: attachment; filename=submissions' . $acronym . '-' . date('YmdHis') . '.csv');
    }

    public function generateReport($request)
    {
        $this->emitHttpHeaders($request);

        $locale = Locale::getLocale();
        $scieloSubmissionsReportFactory = new ScieloSubmissionsReportFactory($this->application, $this->contextId, $this->sections, $this->submissionDateInterval, $this->finalDecisionDateInterval, $locale, $this->includeViews);
        $scieloSubmissionsReport = $scieloSubmissionsReportFactory->createReport();

        $csvFile = fopen('php://output', 'wt');
        $scieloSubmissionsReport->buildCSV($csvFile);
    }

    public function display($request = null, $template = null, $args = null)
    {
        $yearFirstDate = $args[0];
        $todayDate = $args[1];
        $sections = $this->getAvailableSections($this->contextId);
        $sections_options = $this->getSectionsOptions($this->contextId, $sections);

        $templateManager = TemplateManager::getManager();
        $url = $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/templates/mejorabierta.css';
        $templateManager->addStyleSheet('scieloSubmissionsStyleSheet', $url, [
            'priority' => STYLE_SEQUENCE_CORE,
            'contexts' => 'backend',
        ]);
        $templateManager->assign('application', $this->application);
        $templateManager->assign('sections', $sections);
        $templateManager->assign('sections_options', $sections_options);
        $templateManager->assign('years', [$yearFirstDate, $todayDate]);
        $templateManager->assign([
            'breadcrumbs' => [
                [
                    'id' => 'reports',
                    'name' => __('manager.statistics.reports'),
                    'url' => $request->getRouter()->url($request, null, 'stats', 'reports'),
                ],
                [
                    'id' => 'mejorAbierta',
                    'name' => "mejorAbierta"
                ],
            ],
            'pageTitle',
            "mejorAbierta"
        ]);
        $contextId = Application::CONTEXT_JOURNAL;
        $templateManager->assign('journalId', $this->getJournalIdentity($contextId));
        $templateManager->assign('about', $this->getAbout($contextId));
        $templateManager->assign('editorial', $this->getEditiorialTeam($contextId));
        $templateManager->assign('emails', $this->getEmails());
        $templateManager->assign('authors', $this->getAuthors());
        $templateManager->assign('publication', $this->getSubmissions($contextId, [Submission::STATUS_PUBLISHED]));
        $templateManager->assign('declined', $this->getSubmissions($contextId, [Submission::STATUS_DECLINED]));
        $templateManager->assign('queued', $this->getSubmissions($contextId, [Submission::STATUS_QUEUED]));
        $templateManager->assign('scheduled', $this->getSubmissions($contextId, [Submission::STATUS_SCHEDULED]));
        $templateManager->assign('issues', $this->getIssues($contextId));
        $templateManager->assign('comments', $this->getComments($contextId));
        $templateManager->assign('reviewers', $this->getReviewers($contextId));
        //$templateManager->assign('text', $text);
        
        $templateManager->display($this->plugin->getTemplateResource($template));
    }


    private function getAvailableSections($contextId)
    {
        $sections = Repo::section()->getSectionList($contextId);

        $listOfSections = [];
        foreach ($sections as $section) {
            $listOfSections[$section['id']] = $section['title'];
        }
        return $listOfSections;
    }

    public function getSectionsOptions($contextId, $sections)
    {
        $sectionsOptions = [];

        foreach ($sections as $sectionId => $sectionName) {
            $sectionObject = Repo::section()->get($sectionId, $contextId);
            if ($sectionObject->getMetaReviewed() == 1) {
                $sectionsOptions[$sectionObject->getLocalizedTitle()] = $sectionObject->getLocalizedTitle();
            }
        }

        return $sectionsOptions;
    }

    function getJournalIdentity($contextId): string
    {
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);

        $text = "Datos de la revista" . "|";
        $text .= "Nombre: " . implode(",", $context->getSetting('name')) . "|";
        $text .= "ISSN: " . $context->getSetting('printIssn') . "|";
        $text .= "ISSN electrónico: " . $context->getSetting('onlineIssn') . "|";
        $text .= "Entidad: " . $context->getSetting('publisherInstitution');

        return $text;
    }


    function getAbout($contextId): string
    {
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);
        return implode(",", $context->getData('about'));
    }



    function getEditiorialTeam($contextId): string
    {
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);

        return implode(",", $context->getData('editorialTeam'));
    }

    function getEmails()
    {

        $contextId = Application::CONTEXT_JOURNAL;

        // Obtener el repositorio de usuarios
        $userRepo = Repo::user();
        // Crear un collector para filtrar usuarios
        $collector = $userRepo->getCollector()
            ->filterByContextIds([$contextId]); // Filtrar por contexto (revista)

        // Obtener la lista de usuarios
        $users = $userRepo->dao->getMany($collector);


        // Recorrer los usuarios y obtener sus correos electrónicos
        $emails = [];
        foreach ($users as $user) {
            //$user->get
            $emails[] = $user->getEmail();
        }
        $stringemails = implode(',', $emails);

        return $stringemails;
    }

    function getAuthors()
    {
        $contextId = Application::CONTEXT_JOURNAL;

        $users = Repo::user()
            ->getCollector()
            ->filterByRoleIds([Role::ROLE_ID_AUTHOR])
            ->getMany();

        $emails = [];
        foreach ($users as $user) {
            try {
                //$user->get
                //$emails[] = implode(',', $user->getData("givenName"));
                $emails[] = implode(',', $user->getData("familyName"));
                $emails[] = implode(',', $user->getData("affiliation"));

                $emails[] = $user->getData("mailingAddress");
                //$emails[] = $user->reviewer_id;
                $emails[] = $user->getData("phone");
            } catch (\Throwable $th) {
                //throw $th;
                $emails[] = $th->getMessage();
            }

            //$emails[]= $user->getData("affiliation");
            //$emails[] = $user;
        }
        $stringemails = implode(',', $emails);

        return $stringemails;
    }


    function getSubmissions($context, $status)
    {
        $submissions = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$context])
            ->filterByStatus($status)
            ->getMany();


        $filteredElements = $submissions->filter(function ($element) {
            return $element->getData("dateSubmitted") >= date("", 1741873765);
        });


        $json = json_encode($submissions);
        //2025-02-13 00:00:00

        return "" . $filteredElements;
    }

    function getIssues($contextId): string
    {

        $result = Repo::issue()
            ->getCollector()
            ->filterByContextIds([$contextId])
            ->getMany();

        return json_encode($result);
    }

    function getComments($contextId)
    {

        $subs = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->getMany();

        $result = [];
        foreach ($subs as $key => $value) {
            $submissionCommentDao = \DAORegistry::getDAO('SubmissionCommentDAO'); /* @var $submissionCommentDao \SubmissionCommentDAO */
            $commentsIterator = $submissionCommentDao->getSubmissionComments($value->getBestId(), SubmissionComment::COMMENT_TYPE_PEER_REVIEW);

            foreach ($commentsIterator->toArray() as $comment) {
                $result[$comment->getAuthorId()] = $result[$comment->getAuthorId()] ?? '';
                $result[$comment->getAuthorId()] .= ($result[$comment->getAuthorId()] ? '; ' : '') . $comment->getComments();
            }
        }

        return implode(",", $result);
    }

    function getReviewers($params): string
    {
        /*
        //GET REVIEWER
        return json_encode(
            Repo::user()
                ->getCollector()
                ->filterByRoleIds([Role::ROLE_ID_REVIEWER])
                ->getMany()
        );
        */
        
        $data = Repo::user()
                ->getCollector()
                ->filterByRoleIds([Role::ROLE_ID_REVIEWER])
                ->getMany();
        
        foreach ($data as $key => $value) {
           $value->setData("password","none");
           echo $value->getData("password");
          //echo $data->getData($key);
        }
        return json_encode($data);
    }
}
