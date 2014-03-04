<?php

namespace lestore_order\app\model;

use lestore_base\app\model\AttributesCopier;

class Currency{
    use AttributesCopier;

    public $currency;
    public $currencyLocalSymbol;

    public $display;
    public $displaySymbol;
    public $displayLocalSymbol;

    public $goodsAmount;
    public $orderAmount;
    public $bonus;
    public $shippingFee;

    public $goodsAmountExchange;
    public $orderAmountExchange;
    public $bonusExchange;
    public $shippingFeeExchange;

    public $displayGoodsAmountExchange;
    public $displayOrderAmountExchange;
    public $displayBonusExchange;
    public $displayShippingFeeExchange;

    public $rate;
    public $displayRate;

    public function __construct($order){
        $this->copyAttributes($order, true);

        if ($this->display == 0 || $this->currency == $this->display) {
            $this->displayGoodsAmountExchange = $this->goodsAmountExchange;
            $this->displayOrderAmountExchange = $this->orderAmountExchange;
            $this->displayBonusExchange = $this->bonusExchange;
            $this->displayShippingFeeExchange = $this->shippingFeeExchange;
        }

        eval('$this->rate='.$this->rate.';');
        if (!isset($this->displayRate)){
            $this->displayRate = $this->rate;
        }else{
            eval('$this->displayRate='.$this->displayRate.';');
        }
    }
}
