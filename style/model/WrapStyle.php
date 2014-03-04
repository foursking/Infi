<?php
namespace lestore_style\app\model;

use lestore_base\app\model\AttributesCopier;

class WrapStyle extends Style{
    use AttributesCopier;

    public function __construct($base){
        $this->copyAttributes($base, true);
    }

    public function charge($bill, $goods, $exchanger){
        $bill->goodsAmount += $exchanger($goods->wrapPrice);
    }
}

