<?php

namespace Vehicle\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class VehicleController extends AbstractRestfulController
{
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            $response =$vehicleService->addNewVehicleDetailsAPI($params);
            return new JsonModel($response);
        }
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $vehicleService = $this->getServiceLocator()->get('VehicleService');
        $response =$vehicleService->getVehicleDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    public function updateAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            $response =$vehicleService->updateExistsVehicleDetails($params);
            return new JsonModel($response);
        }
    }
}