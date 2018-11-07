<?php
namespace Tyre\Service;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class TyreService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addNewTyreDetailsAPI($params)
    {
        $tyreDb = $this->sm->get('TyreTable');
        return $tyreDb->addTyreDetailsAPI($params);
    }

    public function getTyreDetailsByIdAPI($params)
    {
        $tyreDb = $this->sm->get('TyreTable');
        return $tyreDb->fetchTyreDetailsByIdAPI($params);
    }

    public function updateExistsTyreDetails($params)
    {
        $tyreDb = $this->sm->get('TyreTable');
        return $tyreDb->updateTyreDetails($params);
    }

    public function getTyreDetails($params)
    {
        $tyreDb = $this->sm->get('TyreTable');
        return $tyreDb->fetchTyreDetails($params);
    }

    // Web Service
    public function addTyre($params)
    {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $tyreDb = $this->sm->get('TyreTable');
            $result = $tyreDb->addTyreDetails($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Tyre details added successfully';
            }

        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function getTyreDetailsById($tyreId){
        $tyreDb = $this->sm->get('TyreTable');
        return $tyreDb->fetchTyreDetailsById($tyreId);
    }

    public function updateTyreDetails($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $tyreDb = $this->sm->get('TyreTable');
            $result = $tyreDb->updateTyreDetailsById($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Tyre details updated successfully';
            }
        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
}
