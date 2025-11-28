<?php

namespace APP\plugins\generic\arts\traits;

use APP\core\Application;

trait ContextTrait
{
    function representation()
    {
        $contextId = $this->getJournalId();
        /** @var ContextDAO $contextDao */
        $representationDAO = Application::getRepresentationDAO();
        /** @var Context $context */
        $representation = $representationDAO->getById($contextId);

        echo json_encode($this->anonimizeData($representation));
    }


    function journalIdentity($args, $request, $fromyaml = null)
    {
        $contextId = $this->getJournalId();
        /** @var ContextDAO $contextDao */
        $contextDao = Application::getContextDAO();
        /** @var Context $context */
        $context = $contextDao->getById($contextId);


        $contextArray = (array)$context;
        $contextArray['version'] = Application::get()->getCurrentVersion()->getVersionString();

        if (is_string($args)) {
            $this->initFilter($args, $contextArray);
        }

        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($contextArray);
        } else {
            return $contextArray;
        }
    }


    function about($args, $request, $fromyaml = null)
    {

        $contextId = $this->getJournalId();
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
            return json_encode($data);
        } else {
            return $data;
        }
    }

    function page($args, $request, $fromyaml = null)
    {

        $contextId = $this->getJournalId();
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
            return json_encode($data);
        } else {
            return $data;
        }
    }

    function urls($args, $request, $fromyaml = null)
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

        $data["igualdad"] = $dispatcher->url($request, ROUTE_PAGE, null, 'igualdad%20');
        $data["conservacion"] = $dispatcher->url($request, ROUTE_PAGE, null, 'archivo');
        $data["preservacion"] = $dispatcher->url($request, ROUTE_PAGE, null, 'preservacion');
        $data["estadisticas"] = $dispatcher->url($request, ROUTE_PAGE, null, 'estadisticas');
        $data["codigo_etico"] = $dispatcher->url($request, ROUTE_PAGE, null, 'codigo_etico%20');
        $data["financiacion"] = $dispatcher->url($request, ROUTE_PAGE, null, 'fuentes_de_financiacion');
        $data["autoria"] = $dispatcher->url($request, ROUTE_PAGE, null, '_Autoria');
        $data["politicas"] = $dispatcher->url($request, ROUTE_PAGE, null, 'politicas');
        $data["codigo_etico"] = $dispatcher->url($request, ROUTE_PAGE, null, 'codigo_etico%20');


        if ($fromyaml == null) {
            header('content-type: text/json');
            echo json_encode($data);
        } else {
            return $data;
        }
    }
}
