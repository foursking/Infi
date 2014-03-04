<?php
namespace lestore_shipping\app\model;

class ExpeditedShipping extends AbstractShipping{

    public function __construct($id, $config, $feeConfig, $service){
        parent::__construct($id, $config, $feeConfig, $service);
    }

    public function charge($bill, $order, $exchanger){
        $promotionNow = $this->isPromotionNow();
        return parent::charge($bill, $order, function($fee) use ($exchanger, $promotionNow) {
            if($promotionNow && $fee >= 1){
                $fee -= 1;
            }
            return $exchanger($fee);
        });
    }

    private function isPromotionNow(){
        global $container;
        $shippingOff = $container['siteConf']['shipping_off_70_percent'];
        $promotion_now_time = date("Y-m-d H:i:s");
        #$promotion_now_time = $GLOBALS['promotion_now_time'];
        return $promotion_now_time >= $shippingOff['start_time'] 
            && $promotion_now_time <= $shippingOff['end_time']; 
    }

    protected function adjust($fee){
        if($this->isPromotionNow()){
            return $fee;
        }else{
            return $fee * 1.4;
        }
    }
}
