<?php

namespace APP\plugins\generic\arts;


use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\config\Config;

use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxAction;
use PKP\linkAction\request\RedirectAction;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class ArtsPlugin extends GenericPlugin
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

        if ($page == 'ARTS') {
            $handler = new ArtsHandler($this);
            return true;
        }


        return false;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.generic.arts.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.generic.arts.displayName');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $actionArgs): array
    {



        $actions = parent::getActions($request, $actionArgs);
        if ($this->getEnabled()) {
            $journalPath = $request->getContext()->getPath();
            $baseUrl = $request->getBaseUrl();
            $url = $baseUrl . '/index.php/' . $journalPath . '/ARTS/report';

            array_unshift($actions, new LinkAction(
                'reload',
                new RedirectAction($url),
                __('plugins.generic.arts.configurations')
            ));
        }
        return $actions;
    }
}
