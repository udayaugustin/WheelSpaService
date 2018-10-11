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

    public function getVehicleDetails($params)
    {
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->fetchVehicleDetails($params);
    }

    // Web Service
    public function addVehicle($params)
    {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $vehicleDb = $this->sm->get('VehicleTable');
            $result = $vehicleDb->addVehicleDetails($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Vehicle details added successfully';
            }

        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function getVehicleDetailsById($vehicleId){
        $vehicleDb = $this->sm->get('VehicleTable');
        return $vehicleDb->fetchVehicleDetailsById($vehicleId);
    }

    public function updateVehicleDetails($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $vehicleDb = $this->sm->get('VehicleTable');
            $result = $vehicleDb->updateVehicleDetailsById($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Vehicle details updated successfully';
            }
        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
}
