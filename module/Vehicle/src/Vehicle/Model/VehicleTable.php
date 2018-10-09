<?php
namespace Vehicle\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;


class VehicleTable extends AbstractTableGateway {

    protected $table = 'vehicle_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function addVehicleDetailsAPI($params)
    {   
        if(isset($params->vehicleNo) && trim($params->vehicleNo)!="")
        {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);

            $query = $sql->select()->from('vehicle_details')
                        ->where(array('vehicle_no'=>$params->vehicleNo));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

            if(!isset($rResult->vehicle_id) && trim($rResult->vehicle_id) == ""){
                $data = array(
                    'vehicle_no' => $params->vehicleNo,
                    'user_id' => $params->userId,
                    'vehicle_brand' => $params->vehicleBrand,
                    'vehicle_model' => $params->vehicleModel,
                    'vehicle_type' => $params->vehicleType,
                );
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    $response['status'] = 'success';
                    $response->message ='succesffuly added';
                }else{
                    $response['status'] = 'failed';
                    $response->message ='Not added try again';
                }
            }else{
                $response['status'] = 'failed';
                $response->message ='vehicle no already exists';
            }
        }
        return $response;
    }

    public function fetchAllVehicleListAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                                ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(isset($rResult->role_code) && $rResult->role_code =='admin'){
            $vehicleQuery = $sql->select()->from(array('vd' => 'vehicle_details'))->columns(array('vehicle_id','vehicle_no','vehicle_brand','vehicle_model','vehicle_type'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=vd.user_id',array('name'));
            $vehicleQueryStr = $sql->getSqlStringForSqlObject($vehicleQuery);
            $vehicleResult=$dbAdapter->query($vehicleQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            
            $response['status'] = 'success';
            $response['vehicle-details'] = $vehicleResult;
        }else {
            $response['status']='fail';
            $response->message="Don't have privillage to access";
        }
        return $response;
    }

    public function fetchVehicleDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->authToken) && trim($params->authToken) != ""){
            $vehicleQuery = $sql->select()->from(array('vd' => 'vehicle_details'))->columns(array('vehicle_id','vehicle_no','vehicle_brand','vehicle_model','vehicle_type'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=vd.user_id',array('name'))
                                ->where(array('ud.auth_token' => $params->authToken));
            $vehicleQueryStr = $sql->getSqlStringForSqlObject($vehicleQuery);
            $vehicleResult=$dbAdapter->query($vehicleQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            
            if(isset($vehicleResult) && $vehicleResult != false){
                $response['status'] = 'success';
                $response['vehicle-details'] = $vehicleResult;
            }else{
                $response['status']='fail';
                $response->message="No data found for this user";    
            }
        }else {
            $response['status']='fail';
            $response->message="No data found";
        }
        return $response;
    }

    public function updateVehicleDetails($params)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        $checkQuery = $sql->select()->from('vehicle_details')
                            ->where(array('vehicle_no'=>$params->vehicleNo))
                            ->where('NOT vehicle_id ='.$params->vehicleId);
        $checkQueryStr = $sql->getSqlStringForSqlObject($checkQuery);
        $checkResult=$dbAdapter->query($checkQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(!isset($checkResult->vehicle_id) && $checkResult->vehicle_id == ""){
            
            if(isset($rResult->user_status) && $rResult->user_status == 'active'){
              
                if(isset($params->vehicleId) && trim($params->vehicleId)!="")
                {
                    $data = array(
                        'vehicle_no' => $params->vehicleNo,
                        'user_id' => $params->userId,
                        'vehicle_brand' => $params->vehicleBrand,
                        'vehicle_model' => $params->vehicleModel,
                        'vehicle_type' => $params->vehicleType,
                    );
                    $updateResult = $this->update($data,array('vehicle_id'=>$params->vehicleId));

                    if($updateResult > 0){
                        $response['status'] = 'success';
                        $response['vehicle-details'] = 'Data updated successfully';
                    }else{
                        $response['status'] = 'failed';
                        $response['Vehicle-details'] = 'No updates found';
                    }
                }else{
                    $response['status'] = 'failed';
                    $response['Vehicle-details'] = 'Vehicle not found';
                }
            }else{
                $response['status'] = 'failed';
                $response['Vehicle-details'] = 'You are not have privillage to update';
            }
        }else{
            $response['status'] = 'failed';
            $response['Vehicle-details'] = 'vehicle number already exists.';
        }
        return $response;
    }

}
