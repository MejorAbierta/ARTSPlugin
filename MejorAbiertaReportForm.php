<?php

/**
 * @file plugins/reports/scieloSubmissionsReport/ScieloSubmissionsReportForm.inc.php
 *
 * Copyright (c) 2019-2021 Lepidus Tecnologia
 * Copyright (c) 2020-2021 SciELO
 * Distributed under the GNU GPL v3. For full terms see LICENSE or https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @ingroup plugins_reports_scieloSubmissions
 *
 * @brief SciELO Submissions report Form
 */

namespace APP\plugins\generic\mejorAbierta;

use APP\core\Application;
use APP\facades\Repo;
use APP\plugins\reports\scieloSubmissionsReport\classes\ClosedDateInterval;
use APP\plugins\reports\scieloSubmissionsReport\classes\ScieloSubmissionsReportFactory;
use APP\template\TemplateManager;
use PKP\core\PKPString;
use PKP\facades\Locale;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;

class MejorAbiertaReportForm extends Form
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

        parent::__construct($plugin->getTemplateResource('form.tpl'));
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
        $this->setData('scieloSubmissionsReport', $plugin->getSetting($contextId, 'scieloSubmissionsReport'));
    }



    public function display($request = null, $template = null, $args = null)
    {
        $yearFirstDate = $args[0];
        $todayDate = $args[1];
        $sections = $this->getAvailableSections($this->contextId);
        $sections_options = $this->getSectionsOptions($this->contextId, $sections);

        $templateManager = TemplateManager::getManager();

        $templateManager->assign('operations', [
            'about',
            'author',
            'reviewers',
            'journalIdentity',
            'announcement',
            'category',
            'citation',
            'decision',
            'institution',
            'submissionFile',
            'representation',
            'DAO',
            'submissions',
            'issues',
            'urls',
            'reviews',
            'eventlogs',
            'user'
        ]);

        $folderPath = dirname(__FILE__, 1) . '/configs/';
        $files = scandir($folderPath);
        $filesnames = [];
        foreach ($files as $file) {
            if (is_file($folderPath . '/' . $file)) {
                $filesnames[] = str_replace('.yaml', '', $file);
            }
        }
        $templateManager->assign('filesnames', $filesnames);

        $contextId = Application::CONTEXT_JOURNAL;
        $contextDao = Application::getContextDAO();
        $contextName = $contextDao->getById($contextId)->getName()["en"];

      
        $templateManager->assign('baseURL', $request->getBaseUrl() . '/index.php/' . $contextName . '/mejorAbierta/parseyaml/');
        $templateManager->assign('formats', [
            'json',
            'csv',

        ]);
        $templateManager->assign('auths', [
            'admin',
            'any',

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
                    'id' => 'scieloSubmissionsReport',
                    'name' => __('plugins.reports.scieloSubmissionsReport.displayName')
                ],
            ],
            'pageTitle',
            __('plugins.reports.scieloSubmissionsReport.displayName')
        ]);

        $templateManager->display($this->plugin->getTemplateResource($template));
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
}
