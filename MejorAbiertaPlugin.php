<?php

namespace APP\plugins\generic\mejorAbierta;


use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\config\Config;

use APP\core\Application;

use APP\facades\Repo;


use PKP\core\APIResponse;
use Slim\Http\Request as SlimRequest;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class MejorAbiertaPlugin extends GenericPlugin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register($category, $path, $mainContextId = null)
    {
        $this->addLocaleData();
        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
        if ($success && $this->getEnabled()) {
            // Add a handler to process the biography page

           
            Hook::add('LoadHandler', array($this, 'callbackHandleContent'));
        }
        return $success;
    }

    public function callbackHandleContent($hookName, $args)
    {
        $page = &$args[0];
        $op = &$args[1];
        $handler = &$args[3];
        
        if ($page == 'mejorAbierta') {
            $handler = new MejorAbiertaHandler($this);
            return true;
        }


        return false;
    }
    
    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return "Mejor Abierta Api";
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return "Mejor Abierta Api";
    }
}
