<?php

namespace APP\plugins\generic\arts;

use APP\core\Application;
use APP\template\TemplateManager;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;
use Symfony\Component\Yaml\Yaml;

class ArtsReportForm extends Form
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

        $templateManager->assign('operations', ArtsHandler::DATA_OPERATIONS);
        try {
            $folderPath = dirname(__FILE__, 1) . '/configs/';
            $files = scandir($folderPath);
            $filesnames = [];
            foreach ($files as $file) {
                if (is_file($folderPath . '/' . $file)) {
                    $yaml = Yaml::parseFile($folderPath . '/' . $file);
                    $temp = [];
                                  
                    $temp['name'] = $yaml['report']['config']['name'];
                    $temp['description'] = $yaml['report']['config']['description'];
                    $temp['filename'] = str_replace('.yaml', '', $file);

                    $filesnames[] = $temp;
                }
            }
            $templateManager->assign('filesnames', $filesnames);
        } catch (\Throwable $th) {
            error_log("ARTS error: ".$th->getMessage());
        }


        $context = Application::get()
            ->getRequest()
            ->getContext();

        
        $journalPath = $request->getContext()->getPath();
        $baseUrl = $request->getBaseUrl();
        $url = $baseUrl . '/index.php/' . $journalPath . '/ARTS/parseyaml/';
        
        $templateManager->assign('baseURL', $url);
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
