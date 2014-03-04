<?php
namespace lestore_style\app\model;

class Style{
    public $id;
    public $keyId;
    public $groupId;
    public $name;
    public $value;

    public function __construct(){
        $this->name = strtolower($this->name);
    }

    public function isKey(){
        return $this->keyId = 0;
    }

    public function charge($bill, $goods, $exchanger){
    }
}

