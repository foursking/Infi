<?php
namespace lestore_shipping\app\service;

use lestore_shipping\app\model\ExpeditedShipping;
use lestore_shipping\app\model\StandardShipping;
use lestore_shipping\app\model\SuperSaverShipping;

class ShippingService{

    protected $dao;
    
    public function __construct($dao){
        $this->dao = $dao;
        $config = $dao->getShippingMethods();
        $feeConfig = $dao->getShippingFee();
        
        //TODO safer config index
        $this->shippings[1] = new ExpeditedShipping(1,$config[1][0], $feeConfig[1], $this);
        $this->shippings[2] = new StandardShipping(2,$config[2][0], $feeConfig[2], $this);
        $this->shippings[3] = new SuperSaverShipping(3,$config[3][0], $feeConfig[3], $this);
    }

    public function getShipping($id){
        return $this->shippings[$id];
    }

    public function getValidShippings($order){
        $validShippings = array();
        foreach($this->shippings as $id => $shipping){
            if($shipping->isValid($order)){
                $validShippings[$id] = $shipping;
            }
        }
        return $validShippings;
    }

    public function getArea($countryId, $smId){
        $rs = $this->dao->getAreaMap($countryId);
        $areaMap = array();
        array_walk($rs[$countryId], function(&$item) use (&$areaMap){
            $areaMap[$item['sm_id']] = $item['sm_area'];
        });
        return $areaMap[$smId];
    }
}
