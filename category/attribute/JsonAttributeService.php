<?php
namespace esmeralda\category\attribute;
use esmeralda\base\Node;

class JsonAttributeService extends AbstractAttributeService{
    private $feeder;
    public function __construct($feeder, $categoryId){
        $this->feeder = $feeder;
        $this->categoryId = $categoryId;
        parent::__construct(); 
    }

	protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $id = 'JSON';
        $jsonStr = $this->feeder->getJson($this->categoryId);
        $json = json_decode($jsonStr,true);
        $attributes = $json['base'];
        if(null != $attributes){
            foreach($attributes as $id => $attribute){
                $map[$id] = $this->createNode($id, $attribute);
            }
        }
        $tree = $json['tree'];
        $this->traverseTree($this->root_node_id, $tree, $childRelation, $parentRelation);
    }

    private function createNode($id, $json){
        $c = new Node($id);
        if(isset($json['uname'])){
            $c->uname = $json['uname'];
        }
        if(isset($json['uvalue'])){
            $c->uvalue = $json['uvalue'];
        }
        if(isset($json['bs'])){
            $c->bs = new BitSet(gmp_init($json['bs'], BitSet::BASE));
        }else{
            $c->bs = new BitSet(gmp_init('0', BitSet::BASE));
        }
        return $c;
    }

    private function traverseTree($nodeId, &$children, &$childRelation, &$parentRelation){
        foreach($children as $childId => $grantChildren){
            if(isset($childRelation[$nodeId])){
                $childRelation[$nodeId][] = $childId;
            }else{
                $childRelation[$nodeId] = array($childId);
            }
            if(isset($parentRelation[$childId])){
                $parentRelation[$childId][] = $nodeId;
            }else{
                $parentRelation[$childId] = array($nodeId);
            }
            $this->traverseTree($childId, $grantChildren, $childRelation, $parentRelation);
        }
    }

    public function getNl($language){
        $jsonStr = $this->feeder->getNlJson($this->categoryId, $language);
        $json = json_decode($jsonStr,true);
        $nl = $json['nl'];
        if(isset($nl[$language])){
            return $nl[$language];
        }else{
            return array();
        }
    }

    public function toJson(){
        return $this->feeder->getJson($this->categoryId);
    }

    public function toNlJson($language){
        return $this->feeder->getNlJson($this->categoryId, $language);
    }
}
