<?php
namespace Application\Controller;

use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class ActivateController extends AbstractActionController
{
    public function userAction()
    {
        $userToken=$this->params()->fromRoute('id');
        $userService = $this->getServiceLocator()->get('UserService');
        $result = $userService->setActivate($userToken);
        return $this->redirect()->toUrl("/login");
    }
}
