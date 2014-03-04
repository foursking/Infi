<?php
namespace lestore_style\app\model;

use lestore_base\app\model\AttributesCopier;

class SizeStyle extends Style{
    use AttributesCopier;

    public function __construct($base){
        $this->copyAttributes($base, true);
    }

    private function isPlusSize(){
        return $this->value > 0 && preg_match('/^\d+W/', $this->value);
    }

    public function charge($bill, $goods, $exchanger){
        $isCustomized = false;
        if(isset($goods->customization->extra)){
            $isCustomized = count($goods->customization->extra) > 0;
        }

        global $container;
        $siteConf = $container['siteConf'];
        if($this->isPlusSize()){
            $bill->goodsAmount += $exchanger($siteConf['plussize_fee']);
            return;
        }
        if($isCustomized){
            if(!$this->goods->isAccessory() && $siteConf['custom_fee'] > 0){
                $bill->goodsAmount += $exchanger($siteConf['custom_fee']);
                return;
            }
        }
        return;
    }
}

