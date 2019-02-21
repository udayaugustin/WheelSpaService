<?php

namespace Service\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ServiceController extends AbstractRestfulController
{
    public function addAction() {
        $params = json_decode(file_get_contents('php://input'));
        $serviceService = $this->getServiceLocator()->get('ServiceService');
        $response =$serviceService->addNewServiceDetailsAPI($params);
        return new JsonModel($response);
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $serviceService = $this->getServiceLocator()->get('ServiceService');
        $response =$serviceService->getServiceDetailsByIdAPI($params);
        return new JsonModel($response);
    }

    public function getAllAction() {
        $serviceService = $this->getServiceLocator()->get('ServiceService');
        $response =$serviceService->getServiceDetailsAPI();
        return new JsonModel($response);
    }
    
    public function updateAction() {
        $params = json_decode(file_get_contents('php://input'));
        $serviceService = $this->getServiceLocator()->get('ServiceService');
        $response =$serviceService->updateExistsServiceDetails($params);
        return new JsonModel($response);
    }
}