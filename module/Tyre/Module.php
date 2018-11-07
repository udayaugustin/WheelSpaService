<?php
namespace Tyre;

// Models
use Tyre\Model\TyreTable;

// Service
use Tyre\Service\TyreService;
use Tyre\Service\CommonService;

class Module
{
    public function getServiceConfig() {
        return array(
             'factories' => array(
                  //Table
                'TyreTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new TyreTable($dbAdapter);
                    return $table;
                },

                  //service
                'TyreService' => function($sm) {
                    return new TyreService($sm);
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
