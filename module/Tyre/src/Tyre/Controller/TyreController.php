<?php

namespace Tyre\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class TyreController extends AbstractRestfulController
{
    public function addAction() {
        $params = json_decode(file_get_contents('php://input'));
        $tyreService = $this->getServiceLocator()->get('TyreService');
        $response =$tyreService->addNewTyreDetailsAPI($params);
        return new JsonModel($response);
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $tyreService = $this->getServiceLocator()->get('TyreService');
        $response =$tyreService->getTyreDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    
    public function updateAction() {
        $params = json_decode(file_get_contents('php://input'));
        $tyreService = $this->getServiceLocator()->get('TyreService');
        $response =$tyreService->updateExistsTyreDetails($params);
        return new JsonModel($response);
    }
}