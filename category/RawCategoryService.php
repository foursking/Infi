<?php
namespace esmeralda\category;

class RawCategoryService extends AbstractCategoryService{

    private $feeder;
    public function __construct($feeder){
        $this->feeder = $feeder;
        $this->root_node_id = 1;
        parent::__construct(); 
    }

    protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $raw = $this->feeder->getRaw();
        $map = $raw['base'];
        $childRelation = $raw['c2p'];
        $parentRelation = $raw['p2c'];
        $id = $raw['id'];
    }

    public function getNl($language){
        $rawNl = $this->feeder->getRawNl($language);
        $nl = $rawNl['nl'];
        if(isset($nl[$language])){
            return $nl[$language];
        }else{
            return array();
        }
    }
}
