<?php
namespace lestore_order\app\model;

class Goods{
    public $id;
    public $number;
    public $importantDay;
    public $isGift;

    public $wrapPrice;

    public $productSnapShot;
    public $customization;

    public function __construct(){
        $this->productSnapShot = new ProductSnapShot($this);
        $this->customization = new Customization($this); 
    }

    public function charge($bill, $exchanger){
        $gb = new Bill();
        $gb->goodsAmount = $exchanger($this->productSnapShot->shopPrice);
        $this->customization->charge($gb, $this, $exchanger);
        $gb->goodsAmount += $this->rushOrderFee($exchanger);

        $bill->goodsAmount += $gb->goodsAmount * $this->number;
        return $bill;
    }

    protected function rushOrderFee($exchanger){
        global $container;
        $siteConf = $container['siteConf'];
        if(!$this->productSnapShot->isAccessory() && null != $this->importantDay){
            $datediff = (strtotime($this->importantDay) - strtotime(date('Y-m-d'))) / 86400 + 1;
            foreach ($siteConf['rush_order_fee_date'] as $key => $val){
                if ($datediff >= $val['date_start'] && $datediff <= $val['date_end']){
                    return $exchanger($val['fee']);
                }
            }
        }
        return $exchanger(0);
    }
}
