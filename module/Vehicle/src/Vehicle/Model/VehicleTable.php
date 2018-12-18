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
        if(isset($params->VehicleNo) && trim($params->VehicleNo)!="")
        {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);

            $query = $sql->select()->from('vehicle_details')
                        ->where(array('vehicle_no'=>$params->VehicleNo));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

            if(!isset($rResult->vehicle_id) && trim($rResult->vehicle_id) == ""){
                $data = array(
                    'vehicle_no' => $params->VehicleNo,
                    'user_id' => $params->UserId,
                    'vehicle_name' => $params->VehicleName,
                    'vehicle_brand' => $params->VehicleBrand
                );
                if(isset($params->VehicleModel) && trim($params->VehicleModel) != ""){
                    $data['vehicle_model'] = $params->VehicleModel;
                }
                if(isset($params->VehicleType) && trim($params->VehicleType) != ""){
                    $data['vehicle_type'] = $params->VehicleType;
                }
                if(isset($params->VehicleVersion) && trim($params->VehicleVersion) != ""){
                    $data['vehicle_version'] = $params->VehicleVersion;
                }
                if(isset($params->YearPurchase) && trim($params->YearPurchase) != ""){
                    $data['year_of_purchase'] = $params->YearPurchase;
                }
                if(isset($params->KmDone) && trim($params->KmDone) != ""){
                    $data['km_done'] = $params->KmDone;
                }
                if(isset($params->AvgDrive) && trim($params->AvgDrive) != ""){
                    $data['avg_drive_per_week'] = $params->AvgDrive;
                }
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    $response['Status'] = 'success';
                    $response['Message'] ='succesffuly added';
                }else{
                    $response['Status'] = 'failed';
                    $response['Message'] ='Not added try again';
                }
            }else{
                $response['Status'] = 'failed';
                $response['Message'] ='vehicle no already exists';
            }
        }
        return $response;
    }

    public function fetchVehicleDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->AuthToken) && trim($params->AuthToken) != ""){
            $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->AuthToken,'user_status' => 'active'))
                                ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if(isset($rResult->role_code) && $rResult->role_code =='admin'){
                $vehicleQuery = $sql->select()->from(array('vd' => 'vehicle_details'))->columns(array('*'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=vd.user_id',array('name'));
                $vehicleQueryStr = $sql->getSqlStringForSqlObject($vehicleQuery);
                $vehicleResult=$dbAdapter->query($vehicleQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                
                $response['status'] = 'success';
                $response['VehicleDetails'] = $vehicleResult;
            }else if(isset($rResult->role_code) && $rResult->role_code =='user'){
                $vehicleQuery = $sql->select()->from(array('vd' => 'vehicle_details'))->columns(array('*'))
                                    ->join(array('ud'=>'user_details'),'ud.user_id=vd.user_id',array('name'))
                                    ->where(array('ud.auth_token' => $params->AuthToken));
                $vehicleQueryStr = $sql->getSqlStringForSqlObject($vehicleQuery);
                $vehicleResult=$dbAdapter->query($vehicleQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                if(count($vehicleResult) > 0){
                    $response['Status'] = 'success';
                    $response['VehicleDetails'] = $vehicleResult;
                }else{
                    $response['Status']='fail';
                    $response['Message']="No vehicle found for this user";    
                }
            }else{
                $response['Status']='fail';
                $response['Message']="User not found";    
            }
        }else {
            $response['Status']='fail';
            $response['Message']="No data found";
        }
        return $response;
    }

    public function updateVehicleDetails($params)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);

        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->AuthToken));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(isset($rResult->user_status) && $rResult->user_status == 'active'){
            
            if(isset($params->VehicleId) && trim($params->VehicleId)!="")
            {
                $data = array(
                    'vehicle_no' => $params->VehicleNo,
                    'user_id' => $params->UserId,
                    'vehicle_name' => $params->VehicleName,
                    'vehicle_brand' => $params->VehicleBrand
                );
                if(isset($params->VehicleModel) && trim($params->VehicleModel) != ""){
                    $data['vehicle_model'] = $params->VehicleModel;
                }
                if(isset($params->VehicleType) && trim($params->VehicleType) != ""){
                    $data['vehicle_type'] = $params->VehicleType;
                }
                if(isset($params->VehicleVersion) && trim($params->VehicleVersion) != ""){
                    $data['vehicle_version'] = $params->VehicleVersion;
                }
                if(isset($params->YearPurchase) && trim($params->YearPurchase) != ""){
                    $data['year_of_purchase'] = $params->YearPurchase;
                }
                if(isset($params->KmDone) && trim($params->KmDone) != ""){
                    $data['km_done'] = $params->KmDone;
                }
                if(isset($params->AvgDrive) && trim($params->AvgDrive) != ""){
                    $data['avg_drive_per_week'] = $params->AvgDrive;
                }
                $updateResult = $this->update($data,array('vehicle_id'=>$params->VehicleId));

                if($updateResult > 0){
                    $response['Status'] = 'success';
                    $response['VehicleDetails'] = 'Data updated successfully';
                }else{
                    $response['Status'] = 'failed';
                    $response['VehicleDetails'] = 'No updates found';
                }
            }else{
                $response['Status'] = 'failed';
                $response['VehicleDetails'] = 'Vehicle not found';
            }
        }else{
            $response['Status'] = 'failed';
            $response['VehicleDetails'] = 'You are not have privillage to update';
        }
        return $response;
    }

    // Web Model
    public function fetchVehicleDetails($parameters) {

        $sessionLogin = new Container('credo');
        $aColumns = array('vd.vehicle_no','ud.name','vd.vehicle_brand','vd.vehicle_model','vd.vehicle_type');
        $orderColumns = array('vd.vehicle_no','ud.name','vd.vehicle_brand','vd.vehicle_model','vd.vehicle_type');

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

        $sQuery = $sql->select()->from(array( 'vd' => 'vehicle_details' ))
                            ->join(array('ud' => 'user_details'), 'vd.user_id = ud.user_id', array('name'));

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

            $row = array();
            $row[] = $aRow['vehicle_id'];
            $row[] = $aRow['vehicle_no'];
            $row[] = ucwords($aRow['name']);
            $row[] = ucwords($aRow['vehicle_brand']);
            $row[] = $aRow['vehicle_model'];
            $row[] = $aRow['vehicle_type'];
            $row[] = '<a href="/admin/edit-vehicle/' . base64_encode($aRow['vehicle_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i>Edit</a>';
            $output['aaData'][] = $row;
        }

        return $output;
    }

    public function addVehicleDetails($params)
    {
        if(isset($params['vehicleNo']) && trim($params['vehicleNo'])!="")
        {
            $data = array(
                'vehicle_no' => $params['vehicleNo'],
                'user_id' => base64_decode($params['ownerName']),
                'vehicle_name' => $params['vehicleName'],
                'vehicle_brand' => $params['brand']
            );
            if(isset($params['model']) && trim($params['model']) != ""){
                $data['vehicle_model'] = $params['model'];
            }
            if(isset($params['type']) && trim($params['type']) != ""){
                $data['vehicle_type'] = $params['type'];
            }
            if(isset($params['vehicleVersion']) && trim($params['vehicleVersion']) != ""){
                $data['vehicle_version'] = $params['vehicleVersion'];
            }
            if(isset($params['yearPurchase']) && trim($params['yearPurchase']) != ""){
                $data['year_of_purchase'] = $params['yearPurchase'];
            }
            if(isset($params['kmDone']) && trim($params['kmDone']) != ""){
                $data['km_done'] = $params['kmDone'];
            }
            if(isset($params['avgDrive']) && trim($params['avgDrive']) != ""){
                $data['avg_drive_per_week'] = $params['avgDrive'];
            }
            $this->insert($data);
            $lastInsertedId = $this->lastInsertValue;
        }
        return $lastInsertedId;
    }

    public function fetchVehicleDetailsById($vehicleId)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('vd' => 'vehicle_details'))
                        ->where(array('vd.vehicle_id' => $vehicleId));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        return $rResult;
    }
    
    public function fetchVehicleDetailsByUserId($userId)
    {
        return $this->select(array('user_id'=>$userId))->toArray();
    }

    public function updateVehicleDetailsById($params)
    {
        if(isset($params['vehicleId']) && trim($params['vehicleId'])!="")
        {
            $lastInsertedId = 0;
            $data = array(
                'vehicle_no' => $params['vehicleNo'],
                'user_id' => base64_decode($params['ownerName']),
                'vehicle_name' => $params['vehicleName'],
                'vehicle_brand' => $params['brand']
            );
            if(isset($params['model']) && trim($params['model']) != ""){
                $data['vehicle_model'] = $params['model'];
            }
            if(isset($params['type']) && trim($params['type']) != ""){
                $data['vehicle_type'] = $params['type'];
            }
            if(isset($params['vehicleVersion']) && trim($params['vehicleVersion']) != ""){
                $data['vehicle_version'] = $params['vehicleVersion'];
            }
            if(isset($params['yearPurchase']) && trim($params['yearPurchase']) != ""){
                $data['year_of_purchase'] = $params['yearPurchase'];
            }
            if(isset($params['kmDone']) && trim($params['kmDone']) != ""){
                $data['km_done'] = $params['kmDone'];
            }
            if(isset($params['avgDrive']) && trim($params['avgDrive']) != ""){
                $data['avg_drive_per_week'] = $params['avgDrive'];
            }
            $updateResult = $this->update($data,array('vehicle_id'=>base64_decode($params['vehicleId'])));
            if($updateResult > 0){
                $lastInsertedId = 1;
            }
        }
        return $lastInsertedId;
    }

    public function fetchAllVehicle(){
        return $this->select()->toArray();
    }
}
