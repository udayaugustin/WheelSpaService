<?php
namespace Application\Controller;

use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class AdminController extends AbstractActionController
{

    public function userAction()
    {
        // \Zend\Debug\Debug::dump("hi");die;
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $request = $this->getRequest();
            if ($request->isPost()) {
                $params = $request->getPost();
                $userService = $this->getServiceLocator()->get('UserService');
                $result = $userService->getuserDetails($params);
                return $this->getResponse()->setContent(Json::encode($result));
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

    public function addUserAction()
    {
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $request = $this->getRequest();
            $userService = $this->getServiceLocator()->get('UserService');
            if ($request->isPost()) {
                $params = $request->getPost();
                $result = $userService->addUser($params);
                return $this->redirect()->toUrl("/admin/user");
            }else{
                $roleResult=$userService->getAllRolesDetails();
                return new ViewModel(array(
                    'roleResult' => $roleResult,
                ));
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

    public function editUserAction()
    {
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $userService = $this->getServiceLocator()->get('UserService');
            if($this->getRequest()->isPost())
            {
                $params=$this->getRequest()->getPost();
                $result=$userService->updateUserDetails($params);
                return $this->redirect()->toUrl('/admin/user');
            }
            else
            {
                $userId=base64_decode( $this->params()->fromRoute('id') );
                if($userId!=''){
                    $roleResult=$userService->getAllRolesDetails();
                    $result=$userService->getUserDetailsById($userId);
                    return new ViewModel(array(
                        'result' => $result,
                        'roleResult' => $roleResult,
                    ));
                }else{
                    return $this->redirect()->toUrl("/admin/user");
                }
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

    public function editProfileAction()
    {
        $userService = $this->getServiceLocator()->get('UserService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userService->updateProfile($params);
            return $this->redirect()->toUrl("home");
        }
        else
        {
            $userId=base64_decode( $this->params()->fromRoute('id'));
            if($userId!=''){
            $result=$userService->getuserDetailsById($userId);
            return new ViewModel(array(
                'result' => $result,
            ));
            }else{
                return $this->redirect()->toUrl("home");
            }
        }
    }

    public function vehicleAction()
    {
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $request = $this->getRequest();
            if ($request->isPost()) {
                $params = $request->getPost();
                $userService = $this->getServiceLocator()->get('VehicleService');
                $result = $userService->getVehicleDetails($params);
                return $this->getResponse()->setContent(Json::encode($result));
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

    public function addVehicleAction()
    {
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $request = $this->getRequest();
            $userService = $this->getServiceLocator()->get('UserService');
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            if ($request->isPost()) {
                $params = $request->getPost();
                $result = $vehicleService->addVehicle($params);
                return $this->redirect()->toUrl("/admin/vehicle");
            }else{
                $userResult=$userService->getAllUsers();
                return new ViewModel(array(
                    'userResult' => $userResult,
                ));
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

    public function editVehicleAction()
    {
        $session = new Container('credo');
        if($session->roleCode == 'admin'){
            $userService = $this->getServiceLocator()->get('UserService');
            $vehicleService = $this->getServiceLocator()->get('VehicleService');
            if($this->getRequest()->isPost())
            {
                $params=$this->getRequest()->getPost();
                $result=$vehicleService->updateVehicleDetails($params);
                return $this->redirect()->toUrl('/admin/vehicle');
            }
            else
            {
                $vehicleId=base64_decode( $this->params()->fromRoute('id') );
                if($vehicleId!=''){
                    $userResult=$userService->getAllUsers();
                    $result=$vehicleService->getVehicleDetailsById($vehicleId);
                    return new ViewModel(array(
                        'userResult' => $userResult,
                        'result' => $result,
                    ));
                }else{
                    return $this->redirect()->toUrl("/admin/vehicle");
                }
            }
        }else{
            return $this->redirect()->toUrl("login");
        }
    }

}
