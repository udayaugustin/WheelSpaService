<?php
namespace Tyre\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Tyre\Service\CommonService;


class BackTyreTable extends AbstractTableGateway {

    protected $table = 'back_tyre_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
}
