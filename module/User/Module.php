<?php
namespace User;

// Models
use User\Model\UserTable;

// Service
use User\Service\CommonService;
use User\Service\UserService;

class Module
{
    public function getServiceConfig() {
        return array(
             'factories' => array(
                //Table
                'UserTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },

                //service
                'UserService' => function($sm) {
                    return new UserService($sm);
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
