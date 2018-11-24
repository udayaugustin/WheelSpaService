<?php

namespace Vehicle\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;


class VehicleController extends AbstractRestfulController
{
    public function addAction() {
        $params = json_decode(file_get_contents('php://input'));
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        $response =$vehicleService->addNewVehicleDetailsAPI($params);
        return new JsonModel($response);
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        $response =$vehicleService->getVehicleDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    
    public function updateAction() {
        $params = json_decode(file_get_contents('php://input'));
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        $response =$vehicleService->updateExistsVehicleDetails($params);
        return new JsonModel($response);
    }
}