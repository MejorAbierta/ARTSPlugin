<?php
namespace APP\plugins\generic\mejorAbierta;

use APP\core\Application;
use APP\template\TemplateManager;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorPost;

class MejorAbiertaReportTemplate extends Form
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
    public function __construct($plugin,$template)
    {
        $this->plugin = $plugin;
        $this->application = substr(Application::getName(), 0, 3);
        $request = Application::get()->getRequest();
        $this->contextId = $request->getContext()->getId();

        parent::__construct($plugin->getTemplateResource($template));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    public function initData()
    {
        $contextId = $this->contextId;
        $plugin = $this->plugin;
      
    }



    public function display($request = null, $template = null, $args = null)
    {
  
        $templateManager = TemplateManager::getManager();

        
        $templateManager->assign('application', $this->application);
        $templateManager->assign('data', $args[0]);


        $templateManager->display($this->plugin->getTemplateResource($template));
    }
   
}
