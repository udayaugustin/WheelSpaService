<?php
namespace User\Service;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Exception;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

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
        $result = $userDb->addUserDetailsAPI($params);
        // if($params->LoginType != 'facebook' && $result['Status'] == 'success'){
        //     $config = new \Zend\Config\Reader\Ini();
        //     $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        //     $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        //     $sql = new Sql($dbAdapter);
        //     $uniqueId = $result['AuthToken'];
        //     // Setup SMTP transport using LOGIN authentication
        //     $transport = new SmtpTransport();
        //     $options = new SmtpOptions(array(
        //         'host' => $configResult["email"]["host"],
        //         'port' => $configResult["email"]["config"]["port"],
        //         'connection_class' => $configResult["email"]["config"]["auth"],
        //         'connection_config' => array(
        //             'username' => $configResult["email"]["config"]["username"],
        //             'password' => $configResult["email"]["config"]["password"],
        //             'ssl' => $configResult["email"]["config"]["ssl"],
        //         ),
        //     ));
        //     $domainUrl = $configResult['domain'];
        //     $transport->setOptions($options);
        //     $alertMail = new Mail\Message();
            
        //     $userName = $params->UserName;
        //     $name = $params->Name;
        //     $fromEmail = 'thanaseelan.deforay@gmail.com';
        //     $fromFullName = 'Wheelspa Service';
            
        //     $link = "<a href='".$domainUrl."activate/user/".$uniqueId."'>".$domainUrl."activate/user/".$uniqueId."</a>";
        //     $subject = 'Wheelspa Service User Activation';
        //     $msg = "HI <b>$name</b>,<br><br>Thank you for registering for the Wheelspa Tyre Service Center. You are just a click away from activating your account. To activate your account please click here $link or copy and paste the URL in the browser.<br><br>Regards <br>Wheelspa Tyre Service Center";
        //     $html = new MimePart($msg);
        //     $html->type = "text/html";
        //     $body = new MimeMessage();
        //     $body->setParts(array($html));

        //     $alertMail->setBody($body);
        //     $alertMail->addFrom($fromEmail, $fromFullName);
        //     $alertMail->addReplyTo($fromEmail, $fromFullName);
        //     $alertMail->addTo($userName);
        //     // $alertMail->addCc($ccId);
        //     // $alertMail->addBcc($bccId);
            
        //     $alertMail->setSubject($subject);
        //     $transport->send($alertMail);
        // }
        return $result;
    }
    public function userLoginInApi($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->userLoginDetailsInApi($params);
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

    public function getUserDetails($params)
    {
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchUserDetails($params);
    }

    public function getAllRolesDetails(){
        $roleDb = $this->sm->get('RolesTable');
        return $roleDb->fetchAllRolesDetails();
    }

    public function addUser($params)
    {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $userDb = $this->sm->get('UserTable');
            $result = $userDb->addUserDetails($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'User details added successfully';
            }

        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function getUserDetailsById($userId){
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchUserDetailsById($userId);
    }

    public function updateUserDetails($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $userDb = $this->sm->get('UserTable');
            $result = $userDb->updateUserDetailsById($params);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'User details updated successfully';
            }
        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function setActivate($userToken){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $userDb = $this->sm->get('UserTable');
            $result = $userDb->updateUserStatus($userToken);
            if($result > 0){
                $adapter->commit();
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Your status was activated now login using your credentials.';
            }else{
                $alertContainer = new Container('alert');
                $alertContainer->alertMsg = 'Your status already activated!';
            }
            return $result;
        }
        catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function getAllUsers(){
        $userDb = $this->sm->get('UserTable');
        return $userDb->fetchAllUsers();
    }
}
