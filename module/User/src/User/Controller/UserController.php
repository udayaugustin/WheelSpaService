<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

class UserController extends AbstractRestfulController
{
    public function addAction() 
    {
        $params = json_decode(file_get_contents('php://input'));
        $userService = $this->getServiceLocator()->get('UserService');
        $response =$userService->addNewUserDetailsAPI($params);
        return new JsonModel($response);
    }
    
    public function getAction() 
    {
        $params=$this->getRequest()->getQuery();
        $userService = $this->getServiceLocator()->get('UserService');
        $response =$userService->getUserDetailsByIdAPI($params);
        return new JsonModel($response);
    }
    
    public function loginAction() 
    {
        $params = json_decode(file_get_contents('php://input'));
        $userService = $this->getServiceLocator()->get('UserService');
        $response =$userService->userLoginInApi($params);
        return new JsonModel($response);
    }

    public function updateAction() 
    {
        $params = json_decode(file_get_contents('php://input'));
        $userService = $this->getServiceLocator()->get('UserService');
        $response =$userService->updateExistsUserDetails($params);
        return new JsonModel($response);
    }
}