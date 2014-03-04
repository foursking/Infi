<?php
namespace esmeralda\base;

abstract class AbstractTreeService{
    //const PRINT_TAG = JSON_PRETTY_PRINT;
    const PRINT_TAG = 0;

    private $treeNodes;
    private $childRelation;
    private $parentRelation;
    private $id;

    protected $root_node_id = 0;

	abstract protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id);
    abstract public function getNl($language);

    protected function __construct(){
        $rs = $this->buildTreeNodes($this->treeNodes, 
            $this->childRelation, $this->parentRelation, $this->id);
    }

    public function id(){
        return $this->id;
    }

    public function getRootNodeId(){
        return $this->root_node_id;
    }

    public function getRootNode(){
        return $this->getTreeNode($this->root_node_id);
    }

    public function getTreeNode($nodeId){
        if(isset($this->treeNodes[$nodeId])){
            return $this->treeNodes[$nodeId];
        }else{
            return null;
        }
    }

    public function getAllNodes(){
        return $this->treeNodes;
    }

    public function hasChild($nodeId){
        if(isset($this->childRelation[$nodeId])){
            $children = $this->childRelation[$nodeId];
            if(null != $children){
                return 0 != count($children);
            }else{
                return false;
            }
        }
    }

    public function getChildren($nodeId){
        $children = array();
        if(isset($this->childRelation[$nodeId])){
            $childIds = $this->childRelation[$nodeId];
            if(null != $childIds){
                foreach($childIds as $childId){
                    $child = $this->getTreeNode($childId);
                    if(null != $child){
                        $children[] = $child;
                    }
                } 
            }
        }
        return $children;
    }

    public function getParents($nodeId){
        $parents = array();
        if(isset($this->parentRelation[$nodeId])){
            $parentIds = $this->parentRelation[$nodeId];
            if(null != $parentIds){
                foreach($parentIds as $parentId){
                    $parent = $this->getTreeNode($parentId);
                    if(null != $parent){
                        $parents[] = $parent;
                    }
                }
            }
        }
        return $parents;
    }

    public function getMainParent($nodeId){
        if(isset($this->parentRelation[$nodeId])){
            $parentIds = $this->parentRelation[$nodeId];
            if(null != $parentIds && isset($parentIds[0])){
                $mainParentId = $parentIds[0];
                if(null != $mainParentId){
                    return $this->getTreeNode($mainParentId);
                }
            }
        }
        return null;
    }


    public function nlize($base, $nls){
        $nl = $nls[$base->id()];
        foreach($nl as $key => $value){
            $base->$key = $value;
        }
        return $base;
    }

    public function toJson(){
        $tree = $this->buildTree($this->root_node_id);
        return json_encode(
            array('id' => $this->id, 'tree'=>$tree, 'base' => $this->treeNodes)
            , self::PRINT_TAG 
        );
    }

    protected function buildTree($nodeId){
        $treeNode = array();
        if(isset($this->childRelation[$nodeId])){
            foreach($this->childRelation[$nodeId] as $childId){
                $treeNode[$childId] = $this->buildTree($childId);
            }
        }
        return $treeNode;
    }

    public function toNlJson($language){
        return json_encode(
            array('id'   => $this->id, 
                  'nl' => array($language => $this->getNl($language)))
                  , self::PRINT_TAG 
            );
    }

    public function raw(){
        return array('id' => $this->id, 
            'c2p' => $this->childRelation,
            'p2c' => $this->parentRelation,
            'base' => $this->treeNodes,
        );
    }

    public function rawNl($language){
        return array('id' => $this->id,
            'nl' => array($language => $this->getNl($language)));
    }
}
