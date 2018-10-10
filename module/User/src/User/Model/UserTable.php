<?php
namespace User\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use User\Service\CommonService;


class UserTable extends AbstractTableGateway {

    protected $table = 'user_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function userLoginDetailsInApi($params) {
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $password = sha1($params->password . $configResult["password"]["salt"]);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('username' => $params->username,'password' => $password,'user_status' => 'active'))
                    ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        $statusQuery = $sql->select()->from(array('ud' => 'user_details'))
                            ->where(array('user_id' => $rResult->user_id));
        $statusQueryStr = $sql->getSqlStringForSqlObject($statusQuery);
        $statusResult=$dbAdapter->query($statusQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        
        if(isset($statusResult->user_status) && $statusResult->user_status == 'active'){
            //Get Random string for authToken
            $authToken = $common->generateRandomString();;
        
            if(isset($rResult->user_id) && $rResult->user_id!='') {
                
                $data = array(
                    'auth_token'=>$authToken
                );
                
                $this->update($data,array('user_id'=>$rResult->user_id));
                $response['status']='success';
                $response['user-details']=array(
                                            'userId' => $rResult->user_id,
                                            'userName' => $rResult->username,
                                            'roleCode' => $rResult->role_code,                                        
                                            'authToken' => $authToken,                                        
                                        );
                $response['message']='Logged in successfully';
            }
            else {
                $response['status']='failed';
                $response['message']='Please check your login credentials';
            }
        }else{
            $response['status']='failed';
            $response['message']='User status is inactive';
        }
        return $response;
    }
    
    public function addUserDetailsAPI($params)
    {   
        if(isset($params->userName) && trim($params->userName)!="")
        {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);

            $query = $sql->select()->from('user_details')
                        ->where(array('username'=>$params->userName));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            
            if(!isset($rResult->user_id) && trim($rResult->user_id) == ""){
                $config = new \Zend\Config\Reader\Ini();
                $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
                $password = sha1($params->password . $configResult["password"]["salt"]);
                $data = array(
                    'username' => $params->userName,
                    'role_id' => $params->roleId,
                    'name' => $params->name,
                    'password' => $password,
                    'phone' => $params->mobile,
                );
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    $response['status'] = 'success';
                    $response['message'] ='succesffuly registered';
                }else{
                    $response['status'] = 'failed';
                    $response['message'] ='Not registered try again';
                }
            }else{
                $response['status'] = 'failed';
                $response['message'] ='Username already exists';
            }
        }
        return $response;
    }

    public function fetchAllUserListAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                    ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(isset($rResult->role_code) && $rResult->role_code =='admin'){
            $query = $sql->select()->from(array('ud' => 'user_details'))->columns(array('user_id','name','username','phone','user_status'))
                            ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            
            $response['status'] = 'success';
            $response['user-details'] = $rResult;
        }else {
            $response['status']='fail';
            $response['message']="Don't have privillage to access";
        }
        return $response;
    }

    public function fetchUserDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->authToken) && trim($params->authToken) != ""){
            $userQuery = $sql->select()->from(array('ud' => 'user_details'))->columns(array('user_id','name','username','phone','user_status'))
                            ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'))
                            ->where(array('ud.auth_token' => $params->authToken));
            $userQueryStr = $sql->getSqlStringForSqlObject($userQuery);
            $userResult=$dbAdapter->query($userQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            
            $response['status'] = 'success';
            $response['User-details'] = $userResult;
        }else {
            $response['status']='fail';
            $response['message']="No data found";
        }
        return $response;
    }

    public function updateUserDetails($params)
    {
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        
        //To check login credentials
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                        ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        
        //To check dublication mail
        $checkQuery = $sql->select()->from('user_details')
                            ->where(array('username'=>$params->userName))
                            ->where('NOT user_id ='.$params->userId);
        $checkQueryStr = $sql->getSqlStringForSqlObject($checkQuery);
        $checkResult=$dbAdapter->query($checkQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(!isset($checkResult->user_id) && $checkResult->user_id == ""){
            if(isset($rResult->role_code) && $rResult->role_code == 'admin'){
                if(isset($params->userId) && trim($params->userId)!="")
                {
                    $data = array(
                        'username' => $params->userName,
                        'role_id' => $params->roleId,
                        'name' => $params->name,
                        'phone' => $params->mobile,
                        'user_status' => $params->userStatus,
                    );
                    if($params->password!=''){
                        $password = sha1($params->servPass . $configResult["password"]["salt"]);
                        $data->password = $password;
                    }
                    $updateResult = $this->update($data,array('user_id'=>$params->userId));
                    if($updateResult > 0){
                        $response['status'] = 'success';
                        $response['user-details'] = 'Data updated successfully';
                    }else{
                        $response['status'] = 'failed';
                        $response['user-details'] = 'No updates found';
                    }
                }else{
                    $response['status'] = 'failed';
                    $response['user-details'] = 'User not found';
                }
            }else{
                $response['status'] = 'failed';
                $response['user-details'] = 'You are not have privillage to update';
            }
        }else{
            $response['status'] = 'failed';
            $response['user-details'] = 'Username already exists.';
        }
        return $response;
    }

    // Web Model
    public function loginProcessDetails($params){
        $alertContainer = new Container('alert');
        $logincontainer = new Container('credo');
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        \Zend\Debug\Debug::dump($params);die;
        if(isset($params['userName']) && trim($params['userName'])!="" && trim($params['password'])!=""){
            $password = sha1($params['password'] . $configResult["password"]["salt"]);
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $sQuery = $sql->select()->from(array('ud' => 'user_details'))
                    ->join(array('r' => 'roles'), 'ud.role_id = r.role_id', array('role_code'))
				    ->where(array('ud.email' => $params['userName'], 'ud.password' => $password));
            $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
            $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

            if($rResult) {
                        $logincontainer->userId = $rResult->user_id;
                        $logincontainer->roleId = $rResult->role_id;
                        $logincontainer->roleCode = $rResult->role_code;
                        $logincontainer->userName = ucwords($rResult->user_name);
                        $logincontainer->userEmail = ucwords($rResult->email);
                        if($rResult->role_code != 'admin'){
                            return '/login';
                        }else{
                            return '/user';
                        }
            }else {
                $alertContainer->alertMsg = 'The email id or password that you entered is incorrect';
                return '/login';
            }
        }else {
            $alertContainer->alertMsg = 'The email id or password that you entered is incorrect';
            return '/login';
        }
    }

}
