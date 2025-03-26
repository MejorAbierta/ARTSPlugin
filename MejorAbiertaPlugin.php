<?php
namespace APP\plugins\generic\mejorAbiertaPlugin;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

use APP\core\Application;
use APP\facades\Repo;
use PKP\security\Role;

use PKP\db\DAORegistry;

use APP\submission\Submission;
use PKP\user\User;
use PKP\userGroup\UserGroup;

class MejorAbiertaPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = NULL)
	{
        // Register the plugin even when it is not enabled
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {

            
            Hook::add('TemplateManager::display', array($this, 'callbackTemplateDisplay'));

            
            Hook::add('APIHandler::endpoints', [$this, 'addEndpoints']);
        }

        return $success;
    }
    //Not getting called in 3.4
    public function addEndpoints($hookName, $params) {
        $endpoints =& $params[0];
        $endpoints['GET custom/v1/ping'] = [
            'handler' => [$this, 'handlePing'],
            'roles' => [Role::ROLE_ID_READER]
        ];
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
	{
        return 'Api plugin name';
    }

    /**
     * Provide a description for this plugin
     *     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
	{
        return 'Api plugin desc.';
    }

    function callbackTemplateDisplay($hookName, $args) {
        $templateMgr = $args[0];
        $template = $args[1];

       
        // Verificar si es la plantilla que deseas modificar
        //if ($template !== 'frontend/pages/indexJournal.tpl') {
        //    return false;
        //}

        $request = Application::get()->getRequest();
        $currentUrl = $request->getRequestUrl();



        $contextId = Application::CONTEXT_JOURNAL;
        /*
        public const STATUS_QUEUED = 1;
        public const STATUS_PUBLISHED = 3;
        public const STATUS_DECLINED = 4;
        public const STATUS_SCHEDULED = 5;
        */
        $output = $this->getSubmissions($contextId,[Submission::STATUS_PUBLISHED]);

        $templateMgr->addJavaScript(
            'browserConsolePlugin',
            'console.log(\''.$output  .'\');',
            array('inline' => true)
        );
        
        return false;
    }

    function getSubmissions($context,$status)  {
        $submissions = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$context])
            ->filterByStatus($status)
            ->getMany();
     

        $filteredElements = $submissions->filter(function ($element) {
            return $element->getData("dateSubmitted") >= date("",1741873765);
        });
      

        $json = json_encode($submissions);
        //2025-02-13 00:00:00

        return "".$filteredElements;

    }


    

    function getEmails() {
        
        $currentUser = Application::CONTEXT_JOURNAL;
        $contextId = Application::CONTEXT_JOURNAL;
        
        $submissions = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$contextId])
            ->limit(20)
            ->getMany();
        //return $submissions;

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

    function getAuthors() {
        $contextId = Application::CONTEXT_JOURNAL;



        //GET AUTHORS
        Repo::user()
        ->getCollector()
        ->filterByRoleIds([Role::ROLE_ID_AUTHOR])
        ->getMany();

        /*
        public const ROLE_ID_MANAGER = 16;
        public const ROLE_ID_SITE_ADMIN = 1;
        public const ROLE_ID_SUB_EDITOR = 17;
        public const ROLE_ID_AUTHOR = 65536;
        public const ROLE_ID_REVIEWER = 4096;
        public const ROLE_ID_ASSISTANT = 4097;
        public const ROLE_ID_READER = 1048576;
        public const ROLE_ID_SUBSCRIPTION_MANAGER = 2097152;
        */
        
        $submissions = Repo::submission()
            ->getCollector()
            ->filterByContextIds([$contextId])
            ->limit(20)
            ->getMany();

/*
        $authors=$submissions[0]->getAuthors();
        foreach ($authors as $author) {
            $author->getData("givenName");
        }*/
        //return $submissions;

        // Obtener el repositorio de usuarios
        $userRepo = Repo::user();
        
        $groups = DAORegistry::getDAO('SubscriptionTypeDAO');
        
        $dateTo = date('Ymd', strtotime("-1 day"));
        $dateFrom = date("Ymd", strtotime("-1 year", strtotime($dateTo)));
        $output = "";
        try {
            //$reviewers = $this->getReviewers([$dateFrom, $dateTo, $contextId]);
            //$output = implode(',',$reviewers);
            //$output = implode(',',array_keys(DAORegistry::getDAOs()));

            //$map = & Registry::get('daoMap', true, $this->getDAOMap()); // Ref req'd
       
        } catch (\Throwable $th) {
            //throw $th;
            $output = $th->getMessage();
        }
        //return $output;
       
        // Crear un collector para filtrar usuarios
        $collector = $userRepo->getCollector()
            ->includeReviewerData(true)
            ->filterByContextIds([$contextId]); // Filtrar por contexto (revista)

        // Obtener la lista de usuarios
        $users = $userRepo->dao->getMany($collector);
        
        $emails = [];
        foreach ($users as $user) {
            try {
                //$user->get
                //$emails[] = implode(',', $user->getData("givenName"));
                $emails[] = implode(',', $user->getData("familyName"));
                $emails[] = implode(',', $user->getData("affiliation"));
            
                $emails[] = $user->getData("mailingAddress");
                $emails[] = $user->reviewer_id;
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


    function getReviewers($params) 
    {
        //GET REVIEWER
        return Repo::user()
            ->getCollector()
            ->filterByRoleIds([Role::ROLE_ID_REVIEWER])
            ->getMany();
    }

    function getComments(){
        //COMMENT_TYPE_PEER_REVIEW

        $sub = Repo::submission()->getCollector()
        ->filterByContextIds([0])
        ->getMany();



    }

    function getIssues($contextId){

        Repo::issue()
        ->getCollector()
        ->filterByContextIds([$contextId])
        ->getMany();
    }

    function getAbout($context):string{
        Application::get();
        return strip_tags($context->getData('about', AppLocale::getLocale()));

    }

    function getJournalIdentity($context) : string{
        $text = "Datos de la revista\n";
        $text .= "Nombre: " . $context->getSetting('name', \AppLocale::getLocale()) . "\n";
        $text .= "ISSN: " . $context->getSetting('printIssn') . "\n";
        $text .= "ISSN electrónico: " . $context->getSetting('onlineIssn') . "\n";
        $text .= "Entidad: " . $context->getSetting('publisherInstitution');

        return $text;
    }

    function getEditiorialTeam($context) : string{
        return strip_tags($context->getData('editorialTeam', \AppLocale::getLocale()));
    }
}