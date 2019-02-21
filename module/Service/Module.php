<?php
namespace Service;

// Models
use Service\Model\ServiceTable;

// Service
use Service\Service\ServiceService;
use Service\Service\CommonService;

class Module
{
    public function getServiceConfig() {
        return array(
             'factories' => array(
                  //Table
                'ServiceTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ServiceTable($dbAdapter);
                    return $table;
                },

                //service
                'ServiceService' => function($sm) {
                    return new ServiceService($sm);
                },

                'CommonService' => function($sm) {
                    return new CommonService($sm);
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
