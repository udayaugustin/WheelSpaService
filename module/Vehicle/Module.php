<?php
namespace Vehicle;

// Models
use Vehicle\Model\VehicleTable;

// Service
use Vehicle\Service\VehicleService;

class Module
{
    public function getServiceConfig() {
        return array(
             'factories' => array(
                  //Table
                'VehicleTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new VehicleTable($dbAdapter);
                    return $table;
                },

                  //service
                'VehicleService' => function($sm) {
                    return new VehicleService($sm);
                },
             )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
