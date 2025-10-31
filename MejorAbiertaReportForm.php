<?php

namespace APP\plugins\generic\mejorAbierta;

use APP\core\Application;
use APP\template\TemplateManager;
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

        parent::__construct($plugin->getTemplateResource('form.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }


    public function display($request = null, $template = null, $args = null)
    {

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
            'DAO',
            'submissions',
            'issues',
            'urls',
            'reviews',
            'eventlogs',
            'user'
        ]);
        try {
            $folderPath = dirname(__FILE__, 1) . '/configs/';
            $files = scandir($folderPath);
            $filesnames = [];
            foreach ($files as $file) {
                if (is_file($folderPath . '/' . $file)) {
                    $filesnames[] = str_replace('.yaml', '', $file);
                }
            }
            $templateManager->assign('filesnames', $filesnames);
        } catch (\Throwable $th) {
            error_log("BENJI: ".$th->getMessage());
        }


        $context = Application::get()
            ->getRequest()
            ->getContext();


        $contextId =  $context->getId();

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

        $templateManager->assign('operationsheader', [
            'count',

        ]);

        $templateManager->assign('application', $this->application);


        $templateManager->display($this->plugin->getTemplateResource($template));
    }
}
