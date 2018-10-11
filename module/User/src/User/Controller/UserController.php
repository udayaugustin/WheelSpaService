<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class UserController extends AbstractRestfulController
{
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $response =$userService->addNewUserDetailsAPI($params);
            return new JsonModel($response);
        }
    }
    
    public function getAction() {
        $params=$this->getRequest()->getQuery();
        $userService = $this->getServiceLocator()->get('UserService');
        $response =$userService->getUserDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    public function loginAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $response =$userService->userLoginInApi($params);
            return new JsonModel($response);
        }
    }
    public function updateAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService = $this->getServiceLocator()->get('UserService');
            $response =$userService->updateExistsUserDetails($params);
            return new JsonModel($response);
        }
    }
}