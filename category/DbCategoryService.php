<?php
namespace esmeralda\category;

use esmeralda\base\G11N;

class DbCategoryService extends AbstractCategoryService{

    protected $dao;

    public function __construct($dao){
        $this->dao = $dao;
        parent::__construct(); 
    }

    public function getNl($language){
	    $langId = G11N::langId($language);
		$categories = $this->dao->getCategories($langId);
        $nl = array();
        foreach($categories as $k => $category){
            $id = $category['cat_id'];
            $name = $category['cat_name'];
            $nl[$id] = array('name' =>  $name);//, 'url' => $url);
        }
        return $nl; 
    }
	
	protected function buildTreeNodes(&$map, &$childRelation, &$parentRelation, &$id){
        $id = 'DB';
        $any_langId = G11N::langId('en');
		$categories = $this->dao->getCategories($any_langId);
        foreach($categories as $k => $category){
            $id = $category['cat_id'];
            $category['parentIds'] = array($category['parent_id']);

            $map[$id] = $this->createCategory($id, $category);
            foreach($category['parentIds'] as $pid){
                if(isset($childRelation[$pid])){
                    $childRelation[$pid][] = $id;
                }else{
                    $childRelation[$pid] = array($id);
                }
                $parentRelation[$id] = $category['parentIds'];
            }
        }
    }

    protected function createCategory($id, $category){
        $c = new Category($id);
        $c->url = "-c{$id}";
        if (null != $category){
            foreach($category as $k => $v){
                $c->$k = $v;
            }
        }
        return $c;
    }
	
}
