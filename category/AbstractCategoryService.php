<?php
namespace esmeralda\category;

use esmeralda\base\AbstractTreeService;

abstract class AbstractCategoryService extends AbstractTreeService{
    protected function __construct(){
        parent::__construct();
        $this->root_node_id = 1;
    }

    public function nlize($base, $nls){
        if(isset($base->nlized) && $base->nlized){
            return $base;
        }
        $nl = $nls[$base->id()];
        if(isset($nl['url'])){
            $base->url = $nl['url'];
        }else if(isset($base->url) && substr($base->url,0,2) == '-c'){
            $base->url = urlencode(strtr($nl['name'],' ','-')) . $base->url;
        }
        $base->name = $nl['name'];
        $base->nlized = true;
        return $base;
    }

    public function getCategory($categoryId){
        return $this->getTreeNode($categoryId);
    }
}

