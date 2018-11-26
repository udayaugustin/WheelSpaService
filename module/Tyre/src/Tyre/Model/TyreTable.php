<?php
namespace Tyre\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Tyre\Service\CommonService;


class TyreTable extends AbstractTableGateway {

    protected $table = 'front_tyre_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function addTyreDetailsAPI($params)
    {   
        $backTyreDb = new BackTyreTable($this->adapter);
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->AuthToken,'user_status' => 'active'))
                            ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if(isset($params->TyreType) && trim($params->TyreType) != ""){
            if( isset($rResult->user_id) && trim($rResult->user_id) != "" ){
                $data = array(
                    'user_id' => $rResult->user_id,
                    'vehicle_id' => $params->VehicleId[0],
                    'tyre' => $params->Tyre[0],
                    'tyre_brand' => $params->TyreBrand[0],
                    'tyre_name' => $params->TyreName[0],
                    'tyre_size' => $params->TyreSize[0],
                    'rim_size' => $params->RimSize[0],
                    'tyre_life_remaining' => $params->TyreLife[0],
                    'date_of_parchase' => date('Y-m-d', strtotime($params->DateParchase[0])),
                    'tyre_side' => $params->TyreSide[0],
                    'tyre_type' => $params->TyreType
                );
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    if($params->TyreType == "different"){
                        $backData = array(
                            'front_tyre_id' => $lastInsertedId,
                            'vehicle_id' => $params->VehicleId[1],
                            'tyre' => $params->Tyre[1],
                            'tyre_brand' => $params->TyreBrand[1],
                            'tyre_name' => $params->TyreName[1],
                            'tyre_size' => $params->TyreSize[1],
                            'rim_size' => $params->RimSize[1],
                            'tyre_life_remaining' => $params->TyreLife[1],
                            'date_of_parchase' => date('Y-m-d', strtotime($params->DateParchase[1])),
                            'tyre_side' => $params->TyreSide[1]
                        );
                        $backTyreDb->insert($backData);
                        $lastInsertedBackTyreId = $backTyreDb->lastInsertValue;
                        if($lastInsertedBackTyreId > 0){
                            $response['Status'] = 'success';
                            $response['Message'] ='succesffuly added';
                        }else{
                            $response['Status'] = 'failed';
                            $response['Message'] ='Not added try again';
                        }
                    }else{
                        $response['Status'] = 'success';
                        $response['Message'] ='succesffuly added';
                    }
                }
                else{
                    $response['Status'] = 'failed';
                    $response['Message'] ='Not added try again';
                }
            }else{
                $response['Status'] = 'failed';
                $response['Message'] ='Not privillage to add a tyre information';
            }
        }else{
            $response['Status'] = 'failed';
            $response['Message'] ='Data not found';
        }
        return $response;
    }

    public function fetchtyreDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->AuthToken) && trim($params->AuthToken) != ""){
            $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->AuthToken,'user_status' => 'active'))
                                ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if(isset($rResult->role_code) && $rResult->role_code =='admin'){
                $tyreQuery = $sql->select()->from(array('td' => 'front_tyre_details'))->columns(array('*'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'));
                $tyreQueryStr = $sql->getSqlStringForSqlObject($tyreQuery);
                $tyreResult=$dbAdapter->query($tyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                
                $response['Status'] = 'success';
                $response['TyreDetails'] = $tyreResult;
                foreach($tyreResult as $tyre){
                    $backTyreQuery = $sql->select()->from(array('td' => 'back_tyre_details'))->columns(array('*'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'))
                                ->where(array('td.front_tyre_id' => $tyre['tyre_id']));
                    $backTyreQueryStr = $sql->getSqlStringForSqlObject($backTyreQuery);
                    $backTyreResult=$dbAdapter->query($backTyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
                    $response['TyreDetails']['BackTyre'] = $backTyreResult;
                }
            }else if(isset($rResult->role_code) && $rResult->role_code =='user'){
                $tyreQuery = $sql->select()->from(array('td' => 'tyre_details'))->columns(array('*'))
                                    ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'))
                                    ->where(array('ud.auth_token' => $params->AuthToken));
                $tyreQueryStr = $sql->getSqlStringForSqlObject($tyreQuery);
                $tyreResult=$dbAdapter->query($tyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
                if(isset($tyreResult) && trim($tyreResult) != ""){
                    $response['Status'] = 'success';
                    $response['TyreDetails'] = $tyreResult;
                    $backTyreQuery = $sql->select()->from(array('td' => 'back_tyre_details'))->columns(array('*'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'))
                                ->where(array('td.front_tyre_id' => $tyreResult['tyre_id']));
                    $backTyreQueryStr = $sql->getSqlStringForSqlObject($backTyreQuery);
                    $backTyreResult=$dbAdapter->query($backTyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
                    $response['TyreDetails']['BackTyre'] = $backTyreResult;
                }else{
                    $response['Status']='fail';
                    $response['Message']="No tyre found for this user";    
                }
            }else{
                $response['Status']='fail';
                $response['Message']="No tyre found for this user";    
            }
        }else {
            $response['Status']='fail';
            $response['Message']="No data found";
        }
        return $response;
    }

    public function updateTyreDetails($params)
    {
        $backTyreDb = new BackTyreTable($this->adapter);
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->AuthToken));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(isset($rResult->user_status) && $rResult->user_status == 'active'){
            
            if(isset($params->TyreId) && trim($params->TyreId)!="")
            {
                $data = array(
                    'user_id' => $rResult->user_id,
                    'vehicle_id' => $params->VehicleId[0],
                    'tyre' => $params->Tyre[0],
                    'tyre_brand' => $params->TyreBrand[0],
                    'tyre_name' => $params->TyreName[0],
                    'tyre_size' => $params->TyreSize[0],
                    'rim_size' => $params->RimSize[0],
                    'tyre_life_remaining' => $params->TyreLife[0],
                    'date_of_parchase' => date('Y-m-d', strtotime($params->DateParchase[0])),
                    'tyre_side' => $params->TyreSide[0],
                    'tyre_type' => $params->TyreType
                );
                $updateResult = $this->update($data,array('tyre_id'=>$params->TyreId));
                if($params->TyreType == "different"){
                    $backData = array(
                        'vehicle_id' => $params->VehicleId[1],
                        'tyre' => $params->Tyre[1],
                        'tyre_brand' => $params->TyreBrand[1],
                        'tyre_name' => $params->TyreName[1],
                        'tyre_size' => $params->TyreSize[1],
                        'rim_size' => $params->RimSize[1],
                        'tyre_life_remaining' => $params->TyreLife[1],
                        'date_of_parchase' => date('Y-m-d', strtotime($params->DateParchase[1])),
                        'tyre_side' => $params->TyreSide[1]
                    );
                    $lastInsertedBackTyreId = $backTyreDb->update($backData,array('front_tyre_id'=>$params->TyreId));
                    if($lastInsertedBackTyreId > 0 || $updateResult > 0){
                        $response['Status'] = 'success';
                        $response['Message'] ='Data updated successfully';
                    }else{
                        $response['Status'] = 'failed';
                        $response['Message'] ='No update found';
                    }
                }else{
                    $response['Status'] = 'success';
                    $response['Message'] ='Data updated successfully';
                }
            }else{
                $response['Status'] = 'failed';
                $response['Message'] = 'tyre not found';
            }
        }else{
            $response['Status'] = 'failed';
            $response['Message'] = 'You are not have privillage to update';
        }
        return $response;
    }

    // Web Model
    public function fetchtyreDetails($parameters) {

        $sessionLogin = new Container('credo');
        $aColumns = array('vd.vehicle_no','ud.name','td.tyre','td.tyre_brand','td.tyre_name','tyre_side','tyre_type');
        $orderColumns = array('vd.vehicle_no','ud.name','td.tyre','td.tyre_brand','td.tyre_name','tyre_side','tyre_type');

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

        $sQuery = $sql->select()->from(array( 'td' => 'front_tyre_details' ))
                            ->join(array('ud' => 'user_details'), 'td.user_id = ud.user_id', array('name'))
                            ->join(array('vd' => 'vehicle_details'), 'td.vehicle_id = vd.vehicle_id', array('vehicle_no'));

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
            $row[] = $aRow['vehicle_no'];
            $row[] = ucwords($aRow['name']);
            $row[] = ucwords($aRow['tyre']);
            $row[] = ucwords($aRow['tyre_brand']);
            $row[] = ucwords($aRow['tyre_name']);
            $row[] = ucwords($aRow['tyre_side']);
            $row[] = ucwords($aRow['tyre_type']);
            $row[] = '<a href="/admin/edit-tyre/' . base64_encode($aRow['tyre_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i>Edit</a>';
            $output['aaData'][] = $row;
        }

        return $output;
    }

    public function addtyreDetails($params)
    {
        $backTyreDb = new BackTyreTable($this->adapter);
        $vehicleDb = new \Vehicle\Model\VehicleTable($this->adapter);
        $common = new CommonService;
        if(isset($params['tyreType']) && $params['tyreType']!="")
        {
            $userId = $vehicleDb->select(array('vehicle_id'=>base64_decode($params['vehicleId'][0])))->current();
            $data = array(
                'user_id' => $userId['user_id'],
                'vehicle_id' => base64_decode($params['vehicleId'][0]),
                'tyre' => $params['tyre'][0],
                'tyre_brand' => $params['tyreBrand'][0],
                'tyre_name' => $params['tyreName'][0],
                'tyre_size' => $params['tyreSize'][0],
                'rim_size' => $params-['rimSize'][0],
                'tyre_life_remaining' => $params['tyreLife'][0],
                'date_of_parchase' => date('Y-m-d', strtotime($params['dateParchase'][0])),
                'tyre_side' => $params['tyreSide'][0],
                'tyre_type' => $params['tyreType']
            );
            $this->insert($data);
            $lastInsertedId = $this->lastInsertValue;
            if($lastInsertedId > 0){
                if($params['tyreType'] == "different"){
                    $backData = array(
                        'front_tyre_id' => $lastInsertedId,
                        'vehicle_id' => base64_decode($params['vehicleId'][1]),
                        'tyre' => $params['tyre'][1],
                        'tyre_brand' => $params['tyreBrand'][1],
                        'tyre_name' => $params['tyreName'][1],
                        'tyre_size' => $params['tyreSize'][1],
                        'rim_size' => $params-['rimSize'][1],
                        'tyre_life_remaining' => $params['tyreLife'][1],
                        'date_of_parchase' => date('Y-m-d', strtotime($params['dateParchase'][1])),
                        'tyre_side' => $params['tyreSide'][1],
                    );
                    $backTyreDb->insert($backData);
                }
            }
        }
        return $lastInsertedId;
    }

    public function fetchTyreDetailsById($tyreId)
    {
        $backTyreDb = new BackTyreTable($this->adapter);
        $tyreDetails = $this->select(array('tyre_id' => $tyreId))->current();
        $backTyreDetails = $backTyreDb->select(array('front_tyre_id' => $tyreId))->current();
        return $result = array('tyreDetails'=>$tyreDetails, 'backTyreDetails'=>$backTyreDetails);
    }

    public function updatetyreDetailsById($params)
    {
        $backTyreDb = new BackTyreTable($this->adapter);
        $vehicleDb = new \Vehicle\Model\VehicleTable($this->adapter);
        $common = new CommonService;
        if(isset($params['tyreType']) && $params['tyreType']!="")
        {
            $tyreId = base64_decode($params['frontId']);
            $userId = $vehicleDb->select(array('vehicle_id'=>base64_decode($params['vehicleId'][0])))->current();
            $data = array(
                'user_id' => $userId['user_id'],
                'vehicle_id' => base64_decode($params['vehicleId'][0]),
                'tyre' => $params['tyre'][0],
                'tyre_brand' => $params['tyreBrand'][0],
                'tyre_name' => $params['tyreName'][0],
                'tyre_size' => $params['tyreSize'][0],
                'rim_size' => $params['rimSize'][0],
                'tyre_life_remaining' => $params['tyreLife'][0],
                'date_of_parchase' => date('Y-m-d', strtotime($params['dateParchase'][0])),
                'tyre_side' => $params['tyreSide'][0],
                'tyre_type' => $params['tyreType']
            );
            $updateResult = $this->update($data,array('tyre_id'=>$tyreId));
            if($params['tyreType'] == "different"){
                $backTyreDb->delete("front_tyre_id=" . $tyreId);
                $backData = array(
                    'front_tyre_id' => $tyreId,
                    'vehicle_id' => base64_decode($params['vehicleId'][1]),
                    'tyre' => $params['tyre'][1],
                    'tyre_brand' => $params['tyreBrand'][1],
                    'tyre_name' => $params['tyreName'][1],
                    'tyre_size' => $params['tyreSize'][1],
                    'rim_size' => $params['rimSize'][1],
                    'tyre_life_remaining' => $params['tyreLife'][1],
                    'date_of_parchase' => date('Y-m-d', strtotime($params['dateParchase'][1])),
                    'tyre_side' => $params['tyreSide'][1],
                );
                $backTyreDb->insert($backData);
                $lastInsertedId = $backTyreDb->lastInsertValue;
            }
            if($updateResult >0 || $lastInsertedId > 0){
                $result = 1;
            }else{
                $result = 0;
            }
        }
        return $result;
    }
}
