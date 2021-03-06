<?php
namespace Application;

use Zend\Session\Container;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

// Models
use User\Model\UserTable;
use Vehicle\Model\VehicleTable;
use Application\Model\RolesTable;
use Application\Model\StateTable;

// Service
use Application\Service\CommonService;
use User\Service\UserService;
use Vehicle\Service\VehicleService;
use Tyre\Service\TyreService;
use Application\Service\AdminService;

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
        if (($e->getRouteMatch()->getParam('controller') != 'Application\Controller\Login')) {
            $tempName=explode('Controller',$e->getRouteMatch()->getParam('controller'));
            if(substr($tempName[0], 0, -1) == 'Application'){
                $session = new Container('credo');
                if (!isset($session->userId) || $session->userId == "") {
                        $url = $e->getRouter()->assemble(array(), array('name' => 'login'));
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine('Location', $url);
                        $response->setStatusCode(302);
                        $response->sendHeaders();
                        // To avoid additional processing
                        // we can attach a listener for Event Route with a high priority
                        $stopCallBack = function($event) use ($response) {
                            $event->stopPropagation();
                            return $response;
                        };
                        //Attach the "break" as a listener with a high priority
                        $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
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
                    'RolesTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new RolesTable($dbAdapter);
                        return $table;
                    },
                    'StateTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new StateTable($dbAdapter);
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
                    'TyreService' => function($sm) {
                        return new TyreService($sm);
                    },
                    'AdminService' => function($sm) {
                        return new AdminService($sm);
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
