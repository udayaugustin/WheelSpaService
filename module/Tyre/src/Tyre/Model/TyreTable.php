<?php
namespace Tyre\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Tyre\Service\CommonService;


class TyreTable extends AbstractTableGateway {

    protected $table = 'tyre_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function addTyreDetailsAPI($params)
    {   
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                            ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if(isset($params->tyreBrand) && trim($params->tyreBrand) != ""){
            if( isset($rResult->user_id) && trim($rResult->user_id) != "" ){
                $data = array(
                    'user_id' => $rResult->user_id,
                    'vehicle_id' => $params->vehicleId,
                    'tyre' => $params->tyre,
                    'tyre_brand' => $params->tyreBrand,
                    'tyre_name' => $params->tyreName,
                    'tyre_size' => $params->tyreSize,
                    'rim_size' => $params->rimSize,
                    'tyre_life_remaining' => $params->tyreLife,
                    'date_of_parchase' => $common->dbDateFormat($params->dateParchase),
                    'tyre_side' => $params->tyreSide,
                    'tyre_type' => $params->tyreType
                );
                // \Zend\Debug\Debug::dump($data);die;
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
                if($lastInsertedId > 0){
                    $response['status'] = 'success';
                    $response['message'] ='succesffuly added';
                }else{
                    $response['status'] = 'failed';
                    $response['message'] ='Not added try again';
                }
            }else{
                $response['status'] = 'failed';
                $response['message'] ='Not privillage to add a tyre information';
            }
        }else{
            $response['status'] = 'failed';
            $response['message'] ='Data not found';
        }
        return $response;
    }

    public function fetchtyreDetailsByIdAPI($params) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        if(isset($params->authToken) && trim($params->authToken) != ""){
            $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken,'user_status' => 'active'))
                                ->join(array('r'=>'roles'),'ud.role_id=r.role_id',array('role_code'));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if(isset($rResult->role_code) && $rResult->role_code =='admin'){
                $tyreQuery = $sql->select()->from(array('td' => 'tyre_details'))->columns(array('*'))
                                ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'));
                $tyreQueryStr = $sql->getSqlStringForSqlObject($tyreQuery);
                $tyreResult=$dbAdapter->query($tyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                
                $response['status'] = 'success';
                $response['tyre-details'] = $tyreResult;
            }else if(isset($rResult->role_code) && $rResult->role_code =='user'){
                $tyreQuery = $sql->select()->from(array('td' => 'tyre_details'))->columns(array('*'))
                                    ->join(array('ud'=>'user_details'),'ud.user_id=td.user_id',array('name'))
                                    ->where(array('ud.auth_token' => $params->authToken));
                $tyreQueryStr = $sql->getSqlStringForSqlObject($tyreQuery);
                $tyreResult=$dbAdapter->query($tyreQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                if(isset($tyreResult) && trim($tyreResult) != ""){
                    $response['status'] = 'success';
                    $response['tyre-details'] = $tyreResult;
                }else{
                    $response['status']='fail';
                    $response['message']="No tyre found for this user";    
                }
            }else{
                $response['status']='fail';
                $response['message']="No tyre found for this user";    
            }
        }else {
            $response['status']='fail';
            $response['message']="No data found";
        }
        return $response;
    }

    public function updateTyreDetails($params)
    {
        $common = new CommonService;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('ud' => 'user_details'))->where(array('auth_token' => $params->authToken));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();

        if(isset($rResult->user_status) && $rResult->user_status == 'active'){
            
            if(isset($params->tyreId) && trim($params->tyreId)!="")
            {
                $data = array(
                    'user_id' => $rResult->user_id,
                    'vehicle_id' => $params->vehicleId,
                    'tyre' => $params->tyre,
                    'tyre_brand' => $params->tyreBrand,
                    'tyre_name' => $params->tyreName,
                    'tyre_size' => $params->tyreSize,
                    'rim_size' => $params->rimSize,
                    'tyre_life_remaining' => $params->tyreLife,
                    'date_of_parchase' => $common->dbDateFormat($params->dateParchase),
                    'tyre_side' => $params->tyreSide,
                    'tyre_type' => $params->tyreType
                );
                $updateResult = $this->update($data,array('tyre_id'=>$params->tyreId));

                if($updateResult > 0){
                    $response['status'] = 'success';
                    $response['tyre-details'] = 'Data updated successfully';
                }else{
                    $response['status'] = 'failed';
                    $response['tyre-details'] =     'No updates found';
                }
            }else{
                $response['status'] = 'failed';
                $response['tyre-details'] = 'tyre not found';
            }
        }else{
            $response['status'] = 'failed';
            $response['tyre-details'] = 'You are not have privillage to update';
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

        $sQuery = $sql->select()->from(array( 'td' => 'tyre_details' ))
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
        $common = new CommonService;
        if(isset($params['tyreType']) && $params['tyreType']!="")
        {
            $n = count($params['vehicleId']);
            for($i=0;$i<$n;$i++){
                $data = array(
                    'user_id' => base64_decode($params['ownerName'][$i]),
                    'vehicle_id' => base64_decode($params['vehicleId'][$i]),
                    'tyre' => $params['tyre'][$i],
                    'tyre_brand' => $params['tyreBrand'][$i],
                    'tyre_name' => $params['tyreName'][$i],
                    'tyre_size' => $params['tyreSize'][$i],
                    'rim_size' => $params['rimSize'][$i],
                    'tyre_life_remaining' => $params['tyreLife'][$i],
                    'date_of_parchase' => $common->dbDateFormat($params['dateParchase'][$i]),
                    'tyre_side' => $params['tyreSide'][$i],
                    'tyre_type' => $params['tyreType'],
                );
                // \Zend\Debug\Debug::dump($data);die;
                $this->insert($data);
                $lastInsertedId = $this->lastInsertValue;
            }
        }
        return $lastInsertedId;
    }

    public function fetchTyreDetailsById($tyreId)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('td' => 'tyre_details'))
                        ->where(array('td.tyre_id' => $tyreId));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $rResult=$dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        return $rResult;
    }

    public function updatetyreDetailsById($params)
    {
        $common = new CommonService;
        if(isset($params['tyreId']) && trim($params['tyreId'])!="")
        {
            $lastInsertedId = 0;
            $data = array(
                'user_id' => base64_decode($params->ownerName),
                'vehicle_id' => base64_decode($params->vehicleId),
                'tyre' => $params->tyre,
                'tyre_brand' => $params->tyreBrand,
                'tyre_name' => $params->tyreName,
                'tyre_size' => $params->tyreSize,
                'rim_size' => $params->rimSize,
                'tyre_life_remaining' => $params->tyreLife,
                'date_of_parchase' => $common->dbDateFormat($params->dateParchase),
                'tyre_side' => $params->tyreSide,
                'tyre_type' => $params->tyreType
            );
            $updateResult = $this->update($data,array('tyre_id'=>base64_decode($params['tyreId'])));
            if($updateResult > 0){
                $lastInsertedId = 1;
            }
        }
        return $lastInsertedId;
    }
}
