<?php
namespace Vehicle\Service;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class VehicleService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addNewVehicleDetailsAPI($params)
    {
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->addVehicleDetailsAPI($params);
    }
    public function getAllVehicleListAPI($params)
    {
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->fetchAllVehicleListAPI($params);
    }
    public function getVehicleDetailsByIdAPI($params)
    {
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->fetchVehicleDetailsByIdAPI($params);
    }
    public function updateExistsVehicleDetails($params)
    {
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->updateVehicleDetails($params);
    }
}
