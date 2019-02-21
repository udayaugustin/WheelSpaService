<?php
namespace Service\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;

class ServiceTable extends AbstractTableGateway
{

    protected $table = 'service_detail';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addServiceDetailsAPI($params)
    {
        if (isset($params->ServiceType) && trim($params->ServiceType) != "") {
            $userDb = new \User\Model\UserTable($this->adapter);
            $rResult = $userDb->select(array('auth_token' => $params->AuthToken, 'user_status' => 'active'))->current();
            if (isset($rResult['user_id']) && trim($rResult['user_id']) != "") {
                $sResult = $this->select(array('service_name' => $params->ServiceName))->current();
                if (!isset($sResult['service_id']) && trim($sResult['service_id']) == "") {
                    $data = array(
                        'service_type' => $params->ServiceType,
                        'user_id' => $rResult['user_id'],
                        'service_status' => $params->ServiceStatus
                    );
                    if (isset($params->ServiceName) && trim($params->ServiceName) != "") {
                        $data['service_name'] = $params->ServiceName;
                    }
                    if (isset($params->Price) && trim($params->Price) != "") {
                        $data['price'] = $params->Price;
                    }
                    if (isset($params->Comments) && trim($params->Comments) != "") {
                        $data['remarks'] = $params->Comments;
                    }
                    if (isset($params->ServiceStatus) && trim($params->ServiceStatus) != "") {
                        $data['service_status'] = $params->ServiceStatus;
                    }
                    $this->insert($data);
                    $lastInsertedId = $this->lastInsertValue;
                    $result = $this->select(array('service_id' => $lastInsertedId))->current();
                    if ($lastInsertedId > 0) {
                        $response['Status'] = 'success';
                        $response['Message'] = 'succesffuly inserted';
                        $response['Message'] = $result;
                    } else {
                        $response['Status'] = 'failed';
                        $response['Message'] = 'Not inserted try again';
                    }
                } else {
                    $response['Status'] = 'failed';
                    $response['Message'] = 'Service already exists for this service name';
                }
            } else {
                $response['Status'] = 'failed';
                $response['Message'] = "You don't have a privillage to create a service!";
            }
        } else {
            $response['Status'] = 'failed';
            $response['Message'] = 'No data found!';
        }
        return $response;
    }

    public function fetchServiceDetailsByIdAPI($params)
    {
        if (isset($params->AuthToken) && trim($params->AuthToken) != "") {
            $userDb = new \User\Model\UserTable($this->adapter);
            $rResult = $userDb->select(array('auth_token' => $params->AuthToken, 'user_status' => 'active'))->current();
            if(isset($rResult['user_id']) && trim($rResult['user_id'] != "")){
                $dbAdapter = $this->adapter;
                $sql = new Sql($dbAdapter);
                $sQuery = $sql->select()->from(array('s' => 'service_detail'))->columns(array('ServiceId'=>'service_id','ServiceType' => 'service_type','UserId' => 'user_id','ServiceName' => 'service_name','Price' => 'price','Remarks' => 'remarks','ServiceStatus' => 'service_status'))->where(array('user_id' => $rResult['user_id']));
                $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); 
                $serviceResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                if (isset($serviceResult) && count($serviceResult) > 0) {
                    $response['Status'] = 'success';
                    $response['ServiceDetails'] = $serviceResult;
                }else{
                    $response['Status'] = 'fail';
                    $response['Message'] = "No service found you!";    
                }
            }else{
                $response['Status'] = 'fail';
                $response['Message'] = "No user found";
            }
        } else {
            $response['Status'] = 'fail';
            $response['Message'] = "No data found";
        }
        return $response;
    }

    public function fetchServiceDetailsAPI()
    {
        $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $sQuery = $sql->select()->from(array('s' => 'service_detail'))->columns(array('ServiceId'=>'service_id','ServiceType' => 'service_type','UserId' => 'user_id','ServiceName' => 'service_name','Price' => 'price','Remarks' => 'remarks','ServiceStatus' => 'service_status'))->where(array('service_status' => 'active'));
            $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); 
            return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }

    public function updateServiceDetails($params)
    {
        $serviceId = (int)$params->Id;
        $userDb = new \User\Model\UserTable($this->adapter);
        $rResult = $userDb->select(array('auth_token' => $params->AuthToken, 'user_status' => 'active'))->current();
        if (isset($rResult['user_id']) && trim($rResult['user_id']) != "") {
            $data = array(
                'service_type' => $params->ServiceType,
                'user_id' => $rResult['user_id'],
                'service_status' => $params->ServiceStatus
            );
            if (isset($params->ServiceName) && trim($params->ServiceName) != "") {
                $data['service_name'] = $params->ServiceName;
            }
            if (isset($params->Price) && trim($params->Price) != "") {
                $data['price'] = $params->Price;
            }
            if (isset($params->Remarks) && trim($params->Remarks) != "") {
                $data['remarks'] = $params->Remarks;
            }
            if (isset($params->ServiceStatus) && trim($params->ServiceStatus) != "") {
                $data['service_status'] = $params->ServiceStatus;
            }
            $updateResult = $this->update($data, array('service_id' => $serviceId));
            if ($updateResult > 0) {
                $response['Status'] = 'success';
                $response['Message'] = 'Data updated successfully';
            } else {
                $response['Status'] = 'failed';
                $response['Message'] = 'No updates found';
            }
        } else {
            $response['Status'] = 'failed';
            $response['Message'] = "You are don't have a privillage to update";
        }
        return $response;
    }

    public function fetchServiceDetails($params)
    {
        $aColumns = array('s.service_type', 'ud.name', 's.service_name', 's.price', 's.service_status');

        /* Paging */
        $sLimit = "";
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $sOffset = $params['iDisplayStart'];
            $sLimit = $params['iDisplayLength'];
        }

        /* Ordering */
        $sOrder = "";
        if (isset($params['iSortCol_0'])) {
            for ($i = 0; $i < intval($params['iSortingCols']); $i++) {
                if ($params['bSortable_' . intval($params['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($params['iSortCol_' . $i])] . " " . ($params['sSortDir_' . $i]) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        */

        $sWhere = "";
        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $searchArray = explode(" ", $params['sSearch']);
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
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($params['bSearchable_' . $i]) && $params['bSearchable_' . $i] == "true" && $params['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($params['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($params['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);

        $sQuery = $sql->select()->from(array('s' => 'service_detail'))
            ->join(array('ud' => 'user_details'), 's.user_id = ud.user_id', array('name'));

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
            "sEcho" => intval($params['sEcho']),
            "iTotalRecords" => count($tResult),
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        foreach ($rResult as $aRow) {
            $row = array();
            $row[] = ucwords(str_replace('-',' ',$aRow['service_type']));
            $row[] = ucwords($aRow['name']);
            $row[] = ucwords($aRow['service_name']);
            $row[] = $aRow['price'];
            $row[] = ucwords($aRow['service_status']);
            $row[] = '<a href="/admin/edit-service/' . base64_encode($aRow['service_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i>Edit</a>';
            $output['aaData'][] = $row;
        }

        return $output;
    }

    public function addServiceDetails($params)
    {
        if (isset($params['serviceType']) && trim($params['serviceType']) != "") {
            $data = array(
                'service_type' => $params['serviceType'],
                'user_id' => base64_decode($params['userId']),
                'service_name' => $params['serviceName'],
                'price' => $params['price'],
                'remarks' => $params['remarks'],
                'service_status' => $params['serviceStatus']
            );
            $this->insert($data);
            $lastInsertedId = $this->lastInsertValue;
        }
        return $lastInsertedId;
    }

    public function fetchServiceDetailsById($serviceId)
    {
        return $this->select(array('service_id' => $serviceId))->current();
    }

    public function updateServiceDetailsById($params)
    {
        if (isset($params['serviceId']) && trim($params['serviceId']) != "") {
            $data = array(
                'service_type' => $params['serviceType'],
                'user_id' => base64_decode($params['userId']),
                'service_name' => $params['serviceName'],
                'price' => $params['price'],
                'remarks' => $params['remarks'],
                'service_status' => $params['serviceStatus']
            );
            if (isset($params['service_name']) && trim($params['service_name']) != "") {
                $data['service_name'] = $params['service_name'];
            }
            if (isset($params['price']) && trim($params['price']) != "") {
                $data['price'] = $params['price'];
            }
            if (isset($params['remarks']) && trim($params['remarks']) != "") {
                $data['remarks'] = $params['remarks'];
            }
            $updateResult = $this->update($data, array('service_id' => base64_decode($params['serviceId'])));
        }
        return $updateResult;
    }

    public function fetchAllService()
    {
        return $this->select()->toArray();
    }
}
