<?php
namespace User\Service;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class UserService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addNewUserDetailsAPI($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->addUserDetailsAPI($params);
    }
    public function userLoginInApi($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->userLoginDetailsInApi($params);
    }
    
    public function getAllUserListAPI($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchAllUserListAPI($params);
    }

    public function getUserDetailsByIdAPI($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchUserDetailsByIdAPI($params);
    }

    public function updateExistsUserDetails($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->updateUserDetails($params);
    }

    // Web service
    public function loginProcess($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->loginProcessDetails($params);
    }
}
