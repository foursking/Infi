<?php

namespace esmeralda\currency;

class CurrencyService{
    private $nameMap = array();
    private $idMap = array();

    public function __construct($dao){
        $this->dao = $dao;
        $this->initialize($this->nameMap, $this->idMap);
    }

    protected function initialize(&$nameMap, &$idMap){
        $currencies = $this->dao->getCurrencies();
        foreach($currencies as $currency){
            $nameMap[$currency->name] = $currency;
            $idMap[$currency->id] = $currency;
        }
    }

    public function getCurrency($id){
        return $this->idMap[$id];
    }

    public function getCurrencyByName($name){
        return $this->nameMap[$name];
    }
}

