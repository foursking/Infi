<?php
namespace esmeralda\base;

class Node{
    public $id;

    public function __construct($id){
        $this->id = $id;
    }

    public function id(){
        return $this->id;
    }
}
