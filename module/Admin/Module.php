<?php
namespace Admin;

use Zend\Session\Container;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

// Models
use User\Model\UserTable;
use Vehicle\Model\VehicleTable;

// Service

use Admin\Service\CommonService;
use User\Service\UserService;
use Vehicle\Service\VehicleService;

class Module{
     public function onBootstrap(MvcEvent $e){
          $eventManager        = $e->getApplication()->getEventManager();
          $moduleRouteListener = new ModuleRouteListener();
          $moduleRouteListener->attach($eventManager);

          //no need to call presetter if request is from CLI
          if (php_sapi_name() != 'cli') {
               $eventManager->attach('dispatch', array($this, 'preSetter'), 100);
               $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'dispatchError'), -999);
          }
     }

     public function dispatchError(MvcEvent $event) {
          $error = $event->getError();
          $baseModel = new ViewModel();
          $baseModel->setTemplate('layout/layout');
     }

    public function preSetter(MvcEvent $e) {
        if (($e->getRouteMatch()->getParam('controller') != 'Admin\Controller\Login')) {
            $tempName=explode('Controller',$e->getRouteMatch()->getParam('controller'));
            if(substr($tempName[0], 0, -1) == 'Admin'){
                $session = new Container('credo');
                if (!isset($session->userId) || $session->userId == "") {
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine('Location', '/login');
                        $response->setStatusCode(302);
                        $response->sendHeaders();
                        $stopCallBack = function($event) use ($response) {
                            $event->stopPropagation();
                            return $response;
                        };
                        $e->getAdmin()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
                        return $response;
                    }
            }else{
                if ($e->getRequest()->isXmlHttpRequest()) {
                    return;
                }
            }
        }
    }

     public function getServiceConfig() {
          return array(
               'factories' => array(

                    'UserTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new UserTable($dbAdapter);
                        return $table;
                    },

                    'VehicleTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new VehicleTable($dbAdapter);
                        return $table;
                    },

                    //service
                    'CommonService' => function($sm) {
                         return new CommonService($sm);
                    },

                    'UserService' => function($sm) {
                        return new UserService($sm);
                    },

                    'VehicleService' => function($sm) {
                        return new VehicleService($sm);
                    },
               )
          );
     }

     public function getConfig(){
          return include __DIR__ . '/config/module.config.php';
     }

     public function getAutoloaderConfig(){
          return array(
               'Zend\Loader\StandardAutoloader' => array(
                    'namespaces' => array(
                         __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    ),
               ),
          );
     }


}
