<?php
namespace esmeralda\category\attribute;

class RawAttributeService extends AbstractAttributeService{
    public function __construct($feeder,$categoryId){
        $this->feeder= $feeder;
        $this->categoryId = $categoryId;
        parent::__construct(); 
    }

	protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $raw = $this->feeder->getRaw($this->categoryId);
        $map = $raw['base'];
        $childRelation = $raw['c2p'];
        $parentRelation = $raw['p2c'];
        $id = $raw['id'];
    }

    public function getNl($language){
        $rawNl = $this->feeder->getRawNl($this->categoryId, $language);
        $nl = $rawNl['nl'];
        if(isset($nl[$language])){
            return $nl[$language];
        }else{
            return array();
        }
    }
}
