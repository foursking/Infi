<?php
namespace esmeralda\category;

class JsonCategoryService extends AbstractCategoryService{

    private $feeder;
    public function __construct($feeder){
        $this->feeder = $feeder;
        $this->root_node_id = 1;
        parent::__construct(); 
    }

    protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $id = 'JSON';
        $jsonStr = $this->feeder->getJson();
        $json = json_decode($jsonStr,true);
        $categories = $json['base'];
        foreach($categories as $id => $category){
            $map[$id] = $this->createCategory($id, $category);
        }

        $tree = $json['tree'];
        $this->traverseTree($this->root_node_id, $tree, $childRelation, $parentRelation);
    }

    private function createCategory($id, $json){
        $c = new Category($id);
        foreach($json as $k => $v){
            $c->$k = $v;
        }
        return $c;
    }

    private function traverseTree($categoryId, &$children, &$childRelation, &$parentRelation){
        foreach($children as $childId => $grantChildren){
            if(isset($childRelation[$categoryId])){
                $childRelation[$categoryId][] = $childId;
            }else{
                $childRelation[$categoryId] = array($childId);
            }
            if(isset($parentRelation[$childId])){
                $parentRelation[$childId][] = $categoryId;
            }else{
                $parentRelation[$childId] = array($categoryId);
            }
            $this->traverseTree($childId, $grantChildren, $childRelation, $parentRelation);
        }
    }

    public function getNl($language){
        $jsonStr = $this->feeder->getNlJson($language);
        $json = json_decode($jsonStr,true);
        $nl = $json['nl'];
        if(isset($nl[$language])){
            return $nl[$language];
        }else{
            return array();
        }
    }

    public function toJson(){
        return $this->feeder->getJson();
    }

    public function toNlJson($language){
        return $this->feeder->getNlJson($language);
    }
}
