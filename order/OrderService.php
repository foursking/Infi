<?php
namespace lestore_order\app\service;

use lestore_base\app\service\Node;
use lestore_base\app\service\G11N;

class OrderService{
    private $dao;
    
    public function __construct($dao){
        $this->dao = $dao;
    }

    public function getOrder($orderSn){

    }
}

