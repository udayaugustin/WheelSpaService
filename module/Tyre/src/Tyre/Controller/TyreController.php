<?php

namespace Tyre\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class TyreController extends AbstractRestfulController
{
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $tyreService = $this->getServiceLocator()->get('TyreService');
            $response =$tyreService->addNewTyreDetailsAPI($params);
            return new JsonModel($response);
        }
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $tyreService = $this->getServiceLocator()->get('TyreService');
        $response =$tyreService->getTyreDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    public function updateAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $tyreService = $this->getServiceLocator()->get('TyreService');
            $response =$tyreService->updateExistsTyreDetails($params);
            return new JsonModel($response);
        }
    }
}