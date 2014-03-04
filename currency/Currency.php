<?php
namespace esmeralda\currency;

class Currency{
    public $id;
    public $name;
    public $symbol;
    public $localSymbol;
    public $rate;

    public $disabled;

    public function display($amount, $lang){
        return $amount;
    }
}
