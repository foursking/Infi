<?php
namespace esmeralda\category;

use esmeralda\base\Node;

class Category extends Node{
    public function __construct($id){
        parent::__construct($id);
    }

    public function isAccessory(){
        return $this->is_accessory == '1';
    }
}
