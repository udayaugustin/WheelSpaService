<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class LoginController extends AbstractActionController{

    public function indexAction(){
        $logincontainer = new Container('credo');
        $alertContainer = new Container('alert');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $redirectUrl = $userService->loginProcess($params);
            return $this->redirect()->toUrl($redirectUrl);
        }
        if (isset($logincontainer->userId) && $logincontainer->userId != "") {
            $alertContainer = new Container('alert');
            return $this->redirect()->toUrl("home");
        } else {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }

    public function logoutAction()
    {
        $logincontainer = new Container('credo');
        $alertContainer = new Container('alert');
        $logincontainer->roleId = "";
        $logincontainer->roleCode = ""; 
        $logincontainer->userId = ""; 
        $logincontainer->userName = ""; 
        $logincontainer->userEmail = ""; 

        $logincontainer->getManager()->getStorage()->clear('credo');
        return $this->redirect()->toUrl("login");
    }
}
