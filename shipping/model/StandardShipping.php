<?php
namespace lestore_shipping\app\model;

class StandardShipping extends AbstractShipping{

    public function __construct($id, $config, $feeConfig, $service){
        parent::__construct($id, $config, $feeConfig, $service);
    }

	protected function isFree(){
        global $container;
		$siteConf = $container['siteConf'];
		$start_time = $siteConf['free_shipping_time']['start_time'];
		$end_time = $siteConf['free_shipping_time']['end_time'];
		
		$start_time = strtotime($start_time);
		$end_time = strtotime($end_time);
		$now = strtotime(date("Y-m-d H:i:s"));

		return ($now >= $start_time && $now <= $end_time);
	}

    public function charge($bill, $order, $exchanger){
        return parent::charge($bill, $order, $exchanger); 
    }
}

