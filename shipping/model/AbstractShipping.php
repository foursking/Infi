<?php
namespace lestore_shipping\app\model;

abstract class AbstractShipping{
    protected $id;

    public function __construct($id, $config, $feeConfig, $service){
        $this->id = $id;
        $this->config = $config['config'];

        $feeMap = array();
        array_walk($feeConfig, function(&$item) use (&$feeMap){
            $feeMap[$item['sm_area']] = $item;
        });
        $this->feeMap = $feeMap;
        $this->service = $service;
    }

    public function id(){
        return $this->id;
    }

    public function getTime(){
        $tmp_time = get_process_time(0, $this->id);
        $make_time_end = $make_time;
        if ($vv['sm_id'] == 1) {
            $make_time_end = $make_time_arr[$make_time]['make_time']['min'];
        }

        $process_time = $make_time_end + $tmp_time['shipping_time']['avg'];
        $take_time = $process_time * 24 * 60 * 60;
        $is_w = date('w', $now_time + $take_time);

			/*
			if ($is_w == 0) {
				$around_time = date('l, F d', $now_time + $take_time + 24 * 60 * 60);
			} else {
				$around_time = date('l, F d', $now_time + $take_time);
			}
			$vv['sml_desc'] .= ' ' . $_LANG['page_common_shipping_around_time'] . '<strong>' . $around_time . '</strong>.';
			*/
			/*
			// {{{ shipping_fee additional
			$shipping_fee_additional = 0;
			if ($goods_numbers > 1) {
				$shipping_fee_additional_add = $siteConf['shipping_method_additional_fee'][$vv['sm_id']];
				$shipping_fee_additional = number_format($shipping_fee_additional_add * ($goods_numbers - 1), 2, '.', '');
			}
			// }}}
			

			$vv['sm_fee_value'] = exchangeRate($vv['sm_fee'] + $shipping_fee_additional, $current_currency_id, false, '');
			 */

    }


    public function getTip(){
        if ($lang_code == 'de') {
            $day_lang = 'Tage';
        } else {
            $day_lang = 'days';
        }
        $tip .= '<strong>' . $vv['sml_title'] . ': ' . '</strong> ' . $tmp_time['shipping_time']['min'] . '-' . $tmp_time['shipping_time']['max'] . ' ' . $day_lang . '<br/>';
        
        return $tip;
    }

    public function charge($bill, $order, $exchanger){
        if($this->isFree()){
            return;
        }

        $allFreeCat = true;
        foreach($order->goods as $goods){
			if (!$goods->productSnapShot->isFreeShippingCat()){
                $allFreeCat = false;
			}
        }
        if($allFreeCat){
            return; 
        }

        $area = $this->service->getArea($order->shipment->address->country, $this->id);
        $r = $this->feeMap[$area];

		$fee = 0;
		$basic_fee = $r['basic_fee'];
		$additional_fee = 0;

        $weight = $order->getWeight();
        if ($weight > $r['basic_weight'] && $r['additional_weight'] > 0) {
            $additional_fee = ceil(($weight - $r['basic_weight']) / $r['additional_weight']) * $r['additional_fee'];
        }
        if ($r['discount'] == 0)
            $r['discount'] = 1;
        $fee = ($basic_fee + $additional_fee) * $r['discount'] * (1 + $r['baf']) + $r['fixed_fee'];

        $fee = $this->adjust($fee);

        global $container;
        $CNYrate = $container['currency']->getCurrencyByName('CNY')->rate;
        $tmp = ($fee / $CNYrate);// * 0.9;
        $fee = floor($tmp) + 1.99;

        $bill->shippingFee += $exchanger($fee);
    }

    protected function adjust($fee){
        return $fee;
    }

    public function isValid($order){
        return true;
    }

	protected function isFree() {
		return false;
	}

}
