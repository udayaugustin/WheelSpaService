<?php
namespace Service\Service;

use Zend\Session\Container;
use Exception;

class ServiceService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addNewServiceDetailsAPI($params)
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->addServiceDetailsAPI($params);
    }

    public function getServiceDetailsByIdAPI($params)
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->fetchServiceDetailsByIdAPI($params);
    }

    public function getServiceDetailsAPI()
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->fetchServiceDetailsAPI();
    }

    public function updateExistsServiceDetails($params)
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->updateServiceDetails($params);
    }
    // Web Service
    public function getServiceDetails($params)
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->fetchServiceDetails($params);
    }
    
    public function getServiceDetailsById($params)
    {
        $serviceDb = $this->sm->get('ServiceTable');
        return $serviceDb->fetchServiceDetailsById($params);
    }

    public function addService($params)
    {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $serviceDb = $this->sm->get('ServiceTable');
            $result = $serviceDb->addServiceDetails($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Service details added successfully';
            }

        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function updateServiceDetails($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $serviceDb = $this->sm->get('ServiceTable');
            $result = $serviceDb->updateServiceDetailsById($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Service details updated successfully';
            }
        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
}
