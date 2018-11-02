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
        \Zend\Debug\Debug::dump($params);die;
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $password = sha1($params->password . $configResult["password"]["salt"]);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('username' => $params->username,'password' => $password))
                    ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        
        if(isset($rResult->user_id) && $rResult->user_id !=''){
            //Get Random string for authToken
            $authToken = $common->generateRandomString();
        
            if(isset($rResult->user_status) && $rResult->user_status !='inactive') {
                
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
                $response['message']='User status is inactive';
            }
        }else{
            $response['status']='failed';
            $response['message']='Please check your login credentials';
        }
        return $response;
    }
    
    public function addUserDetailsAPI($params)
    {   
        $common = new CommonService;
        if(isset($params->userName) && trim($params->userName)!="")
        {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);

            $query = $sql->select()->from('user_details')
                        ->where(array('username'=>$params->userName));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            $authToken = $common->generateRandomString();

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
                    'user_dob' => $common->dbDateFormat($params['dob']),
                    'pincode' => $params['pincode'],
                    'state' => $params['state'],
                    'city' => $params['city'],
                    'street_address' => $params['address'],
                    'auth_token' => $authToken,
                );
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    $response['status'] = 'success';
                    $response['authToken'] = $authToken;
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

    public function fetchUserDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->authToken) && trim($params->authToken) != ""){
            $userQuery = $sql->select()->from(array('ud' => 'user_details'))->columns(array('user_id','name','username','phone','user_dob','state','city','street_address','pincode','user_status'))
                            ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'))
                            ->where(array('ud.auth_token' => $params->authToken));
            $userQueryStr = $sql->getSqlStringForSqlObject($userQuery);
            $userResult=$dbAdapter->query($userQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            
            if(isset($userResult->role_code) && $userResult->role_code =='admin'){
                $query = $sql->select()->from(array('ud' => 'user_details'))->columns(array('user_id','name','username','phone','user_dob','state','city','street_address','pincode','user_status'))
                                ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
                $queryStr = $sql->getSqlStringForSqlObject($query);
                $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                
                $response['status'] = 'success';
                $response['user-details'] = $rResult;
            }else if( isset($userResult->role_code) ){
                $response['status'] = 'success';
                $response['user-details'] = $userResult;
            }else{
                $response['status']='fail';
                $response['message']="User not found";
            }
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
        $common = new CommonService;
        //To check login credentials
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                        ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        
        if(isset($params->userId) && trim($params->userId)!=""){
            $data = array(
                'username' => $params->userName,
                'role_id' => $params->roleId,
                'name' => $params->name,
                'phone' => $params->mobile,
                'user_dob' => $common->dbDateFormat($params['dob']),
                'pincode' => $params['pincode'],
                'state' => $params['state'],
                'city' => $params['city'],
                'street_address' => $params['address'],
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
        return $response;
    }

    // Web Model
    public function loginProcessDetails($params){
        $alertContainer = new Container('alert');
        $logincontainer = new Container('credo');
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        if(isset($params['userName']) && trim($params['userName'])!="" && trim($params['password'])!=""){
            $password = sha1($params['password'] . $configResult["password"]["salt"]);
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $sQuery = $sql->select()->from(array('ud' => 'user_details'))
                    ->join(array('r' => 'roles'), 'ud.role_id = r.role_id', array('role_code'))
                    ->where(array('ud.username' => $params['userName'], 'ud.password' => $password))
                    ->where(array('r.role_code' => 'admin'));
            $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
            $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

            if($rResult) {
                        $logincontainer->userId = $rResult->user_id;
                        $logincontainer->roleId = $rResult->role_id;
                        $logincontainer->roleCode = $rResult->role_code;
                        $logincontainer->userName = ucwords($rResult->name);
                        $logincontainer->userEmail = ucwords($rResult->username);
                        if($rResult->role_code != 'admin'){
                            return 'login';
                        }else{
                            return '/';
                        }
            }else {
                $alertContainer->alertMsg = "You don't have a privillage to access";
                return 'login';
            }
        }else {
            $alertContainer->alertMsg = 'The email id or password that you entered is incorrect';
            return 'login';
        }
    }

    public function fetchUserDetails($parameters) {

        $sessionLogin = new Container('credo');
        $aColumns = array('ud.name','r.role_name','ud.username','ud.phone','ud.user_status');
        $orderColumns = array('ud.name','r.role_name','ud.username','ud.phone','ud.user_status');

        /* Paging */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /* Ordering */
        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                        $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                        $sWhereSub .= "(";
                } else {
                        $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);

                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
                if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                    if ($sWhere == "") {
                        $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                    } else {
                        $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                    }
                }
        }

        /*
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $roleId=$sessionLogin->roleId;

        $sQuery = $sql->select()->from(array( 'ud' => 'user_details' ))
                            ->join(array('r' => 'roles'), 'ud.role_id = r.role_id', array('role_name'));

        if (isset($sWhere) && $sWhere != "") {
                $sQuery->where($sWhere);
        }

        if (isset($sOrder) && $sOrder != "") {
                $sQuery->order($sOrder);
        }

        if (isset($sLimit) && isset($sOffset)) {
                $sQuery->limit($sLimit);
                $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $tQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($tResult);
        $output = array(
                "sEcho" => intval($parameters['sEcho']),
                "iTotalRecords" => count($tResult),
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
        );
        foreach ($rResult as $aRow) {
            if($aRow['role_id'] == '2'){
                $row = array();
                $row[] = ucwords($aRow['name']);
                $row[] = ucwords($aRow['role_name']);
                $row[] = $aRow['username'];
                $row[] = $aRow['phone'];
                $row[] = ucwords($aRow['user_status']);
                $row[] = '<a href="/admin/edit-user/' . base64_encode($aRow['user_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i>Edit</a>';
                $output['aaData'][] = $row;
            }
        }

        return $output;
    }

    public function addUserDetails($params)
    {
        if(isset($params['name']) && trim($params['name'])!="")
        {
            $common = new CommonService;
            $config = new \Zend\Config\Reader\Ini();
            $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
            $password = sha1($params['password'] . $configResult["password"]["salt"]);
            $data = array(
                'name' => $params['name'],
                'role_id' => base64_decode($params['roleName']),
                'username' => $params['email'],
                'password' => $password,
                'phone' => $params['mobile'],
                'user_dob' => $common->dbDateFormat($params['dob']),
                'pincode' => $params['pincode'],
                'state' => $params['state'],
                'city' => $params['city'],
                'street_address' => $params['address'],
                'user_status' => $params['userStatus']
                
            );
            $this->insert($data);
            $lastInsertedId = $this->lastInsertValue;
        }
        return $lastInsertedId;
    }

    public function fetchUserDetailsById($userId)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('ud' => 'user_details'))
                        ->where(array('ud.user_id' => $userId));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        return $rResult;
    }

    public function updateUserDetailsById($params)
    {
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $common = new CommonService;
        if(isset($params['userId']) && trim($params['userId'])!="")
        {
            $lastInsertedId = 0;
            $data = array(
                'name' => $params['name'],
                'role_id' => base64_decode($params['roleName']),
                'username' => $params['email'],
                'phone' => $params['mobile'],
                'user_dob' => $common->dbDateFormat($params['dob']),
                'pincode' => $params['pincode'],
                'state' => $params['state'],
                'city' => $params['city'],
                'street_address' => $params['address'],
                'user_status' => $params['userStatus']
            );
            if($params['password']!=''){
                $password = sha1($params['password'] . $configResult["password"]["salt"]);
                $data['password'] = $password;
            }
            // \Zend\Debug\Debug::dump($data);die;
            $updateResult = $this->update($data,array('user_id'=>base64_decode($params['userId'])));
            if($updateResult > 0){
                $lastInsertedId = 1;
            }
        }
        return $lastInsertedId;
    }

    public function fetchAllUsers(){
        return $this->select()->toArray();
    }
}
