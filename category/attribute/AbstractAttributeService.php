<?php
namespace esmeralda\category\attribute;
use esmeralda\base\AbstractTreeService;

abstract class AbstractAttributeService extends AbstractTreeService{
    protected function __construct(){
        parent::__construct();
    }

    public function nlize($base, $nls){
        if(isset($base->nlized) && $base->nlized){
            return $base;
        }

        $nl = $nls[$base->id()];
        if(isset($nl['url'])){
            $base->url = $nl['url'];
        }else{
            $parent = $this->getMainParent($base->id());
            if(null != $parent){
                $base->url = urlencode(strtr($nl['value'],' ','-')) . 
                    '_p' . $parent->id() .
                    'i' . $base->id();
            }
        }
        parent::nlize($base,$nls);
        $base->nlized = true;
        return $base;
    }

    //backward compatibility for PHP 5.3
    public function toJson(){
        $nodes = $this->getAllNodes();
        if(null != $nodes){
            foreach($nodes as $k => &$node){
                if(isset($node->bs)){
                    $node->bs = $node->bs->jsonSerialize();
                }
            }
        }
        $tree = $this->buildTree($this->root_node_id);
        return json_encode(
            array('id' => $this->id(), 'tree'=>$tree, 'base' => $nodes)
            , self::PRINT_TAG 
        );
    }
}
