<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use User\Service\CommonService;

class RolesTable extends AbstractTableGateway {

    protected $table = 'roles';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAllRolesDetails(){
        return $this->select()->toArray();
    }
}
